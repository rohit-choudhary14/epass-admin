<script>
    function isValidEmail(email) {
        if (!email || typeof email !== "string") {
            return false;
        }

        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
    }

    function registerAdvocate(enrollNo) {

        const passType = document.querySelector(
            "#advRegisterForm input[name='passtype']:checked"
        )?.value;

        const mobile = document.getElementById("adv_mobile").value.trim();
        const email = document.getElementById("adv_email").value.trim();
        const address = document.getElementById("adv_address").value.trim();

        if (!passType || !mobile || !address) {
            showError("Please fill all required fields");
            return;
        }

        let fd = new FormData();
        fd.append("enroll_no", safeEncode(enrollNo));
        fd.append("passtype", passType);
        fd.append("mobile", safeEncode(mobile));
        fd.append("email", safeEncode(email));
        fd.append("address", safeEncode(address));

        showLoader();

        fetch("/HC-EPASS-MVC/public/index.php?r=auth/registerAdvocateAjax", {
                method: "POST",
                body: fd
            })
            .then(res => res.json())
            .then(resp => {
                hideLoader();
                if (resp.status !== "OK") {
                    showError(resp.message);
                    return;
                }
                showSuccess(resp.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(() => {
                hideLoader();
                showError("Server error");
            });
    }

    function showAdvocateDetails(data) {

        const enrollInput = document.getElementById("enroll_no");
        enrollInput.readOnly = true;
        enrollInput.style.background = "#f3f4f6";

        const box = document.getElementById("adv-details-box");
        box.style.display = "block";
        const hasMobile = !!data.mobile;
        const hasEmail = !!data.email;
        const hasAddress = !!data.address;

        box.innerHTML = `
        <div class="form-group">
            <label>Advocate Name</label>
            <input type="text" value="${data.adv_name}" readonly>
        </div>

        <div class="form-group">
            <label>Mobile *</label>
            <input type="text"
                   id="adv_mobile"
                   value="${data.mobile || ''}"
                   ${hasMobile ? 'readonly style="background:#f3f4f6"' : ''}>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="text"
                   id="adv_email"
                   value="${data.email || ''}"
                   ${hasEmail ? 'readonly style="background:#f3f4f6"' : ''}>
        </div>

        <div class="form-group">
            <label>Address *</label>
            <textarea id="adv_address"
                ${hasAddress ? 'readonly style="background:#f3f4f6"' : ''}>${data.address || ''}</textarea>
        </div>

        <div class="form-group">
            <label><b>Pass Type</b></label>
            <label style="margin-left:15px">
                <input type="radio" name="passtype" value="1"> Sr. Advocate
            </label>
            <label style="margin-left:15px">
                <input type="radio" name="passtype" value="2"> Advocate (For Self and Litigants)
            </label>
            <label style="margin-left:15px">
                <input type="radio" name="passtype" value="3"> Party in Person
            </label>
        </div>
    `;

        const btn = document.querySelector("#advRegisterForm button[type]");
        btn.type = "button";
        btn.innerText = "Confirm & Register Advocate";

        btn.onclick = function(e) {
            e.preventDefault();

            const passTypeEl = document.querySelector(
                "#advRegisterForm input[name='passtype']:checked"
            );
            if (!passTypeEl) {
                showError("Please select Pass Type.");
                return;
            }
            if (!hasMobile && !document.getElementById("adv_mobile").value.trim()) {
                showError("Mobile number is required");
                return;
            }
            if (!hasAddress && !document.getElementById("adv_address").value.trim()) {
                showError("Address is required");
                return;
            }
            if (!hasEmail) {
                const emailVal = document.getElementById("adv_email").value.trim();

                if (!emailVal) {
                    showError("Email is required");
                    return;
                }

                if (!isValidEmail(emailVal)) {
                    showError("Please enter a valid email address");
                    return;
                }
            }
            const ok = confirm(
                `Are you sure you want to register this advocate?\n\n` +
                `Name: ${data.adv_name}\n` +
                `Enrollment No: ${data.adv_reg || data.enroll_no}\n` +
                `Pass Type: ${passTypeEl.nextSibling.textContent.trim()}`
            );

            if (!ok) return;
            initOtpFlow({
                mobile: document.getElementById("adv_mobile").value.trim(),
                purpose: "COURT_PASS",
                role: "PARTY",
                payload: {
                    data
                },
                onSuccess: (p) => {
                    registerAdvocate(data.adv_reg || data.enroll_no);
                }
            });
        };
    }





    function checkEnrollAndFetchDetails() {

        const form = document.getElementById("advRegisterForm");
        const enrollNo = form.enroll_no.value.trim();

        if (enrollNo === "") {
            showError("Enrollment number is required.");
            return;
        }

        let fd = new FormData();
        fd.append("enroll_no", safeEncode(enrollNo));

        showLoader();

        fetch("/HC-EPASS-MVC/public/index.php?r=auth/findAdvDetails", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(resp => {
                hideLoader();

                if (resp.status === "NOT_FOUND") {
                    showError(resp.message || "Advocate not found.");
                    return;
                }

                if (resp.status === "FOUND") {
                    showAdvocateDetails(resp.data);
                }
            })
            .catch(() => {
                hideLoader();
                showError("Server error.");
            });
    }



    function showAdvocateRegisterForm(message) {
        const box = document.getElementById("case-result");

        box.style.display = "block";
        box.innerHTML = `
        <div class="card form-container new-pass-box" style="margin-top:25px;border-left:5px solid #dc2626">
            <h3 class="pass-title" style="color:#dc2626">Advocate Registration Required</h3>

            <p style="font-weight:600;color:#991b1b">
                ${message || 'Advocate is not registered in the Gate Pass system.'}
            </p>

           <form id="advRegisterForm" onsubmit="checkEnrollAndFetchDetails(); return false;">

    <div class="form-group">
        <label>Enrollment Number</label>
        <input type="text" name="enroll_no" id="enroll_no" required>
    </div>

    <div id="adv-details-box" style="display:none"></div>

    <button type="submit" class="generate-btn">
        Search Advocate
    </button>

    <button type="button" class="generate-btn" style="background:#6b7280;margin-left:10px"
        onclick="location.reload()">
        Cancel
    </button>
</form>
        </div>
    `;
    }


    async function fetchMobileByEnroll(enroll) {
        let fd = new FormData();
        fd.append("enroll_no", safeEncode(enroll));
        showLoader();
        try {
            const response = await fetch(
                "/HC-EPASS-MVC/public/index.php?r=auth/findAdvDetails", {
                    method: "POST",
                    body: fd
                }
            );
            const text = await response.text();
            let res = JSON.parse(text);
            hideLoader();
            if (res.status !== "FOUND" || !res.data || !res.data.mobile) {
                throw new Error(res.message || "Mobile not found");
            }

            return res.data.mobile;

        } catch (err) {
            hideLoader();
            showError("Unable to fetch advocate mobile number");
            console.error("FETCH MOBILE ERROR:", err);
            return null;
        }
    }
</script>