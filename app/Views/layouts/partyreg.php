<script>
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function isValidMobile(mobile) {
        return /^[6-9][0-9]{9}$/.test(mobile);
    }

    function registerParty() {
        const name = document.getElementById("party_name").value.trim();
        const mobile = document.getElementById("party_mobile").value.trim();
        const email = document.getElementById("party_email").value.trim();
        const address = document.getElementById("address").value.trim();

        const est = document.querySelector(
            "#partyRegisterForm input[name='establishment']:checked"
        )?.value;
        if (!name || !mobile || !email || !est || !address) {
            showError("Please fill all required fields");
            return;
        }
        if (!isValidMobile(mobile)) {
            showError("Enter valid 10 digit mobile number");
            return;
        }
        if (!isValidEmail(email)) {
            showError("Please enter valid email address");
            return;
        }
        const ok = confirm(
            `Confirm Party Registration?\n\n` +
            `Name: ${name}\n` +
            `Mobile: ${mobile}\n` +
            `Email: ${email}\n` +
            `Establishment: ${est}`
        );

        if (!ok) return;

        let fd = new FormData();
        fd.append("party_name", safeEncode(name));
        fd.append("mobile", safeEncode(mobile));
        fd.append("email", safeEncode(email));
        fd.append("estt", safeEncode(est));
        fd.append("address", safeEncode(address));

        showLoader();

        fetch("/HC-EPASS-MVC/public/index.php?r=auth/registerPartyAjax", {
                method: "POST",
                body: fd
            })
            .then(res => res.json())
            .then(resp => {
                hideLoader();

                if (resp.status !== "OK") {
                    showError(resp.message || "Registration failed");
                    return;
                }

                showSuccess(resp.message || "Party registered successfully");
                setTimeout(() => location.reload(), 1200);
            })
            .catch(() => {
                hideLoader();
                showError("Server error");
            });
    }

    function showPartyRegisterForm(message, partyName) {

        const box = document.getElementById("case-result");
        box.style.display = "block";

        box.innerHTML = `
        <div class="card form-container new-pass-box"
             style="
                margin-top:25px;
                border-left:5px solid #dc2626;
                padding:24px;
             ">

            <h3 class="pass-title" style="color:#dc2626;margin-bottom:8px">
                Party Registration Required
            </h3>

            <p style="
                font-weight:600;
                color:#991b1b;
                margin-bottom:20px;
            ">
                ${message || 'Party is not registered in the Gate Pass system.'}
            </p>

            <form id="partyRegisterForm" onsubmit="registerParty(); return false;">

                <div class="form-group" style="margin-bottom:15px">
                    <label class="form-label">* Party Name</label>
                    <input type="text" id="party_name" class="form-control"
                           placeholder="Enter party name" value="${partyName}">
                </div>

                <div class="form-group" style="margin-bottom:15px">
                    <label class="form-label">* Mobile No</label>
                    <input type="text" id="party_mobile" class="form-control"
                           placeholder="Enter 10 digit mobile number">
                </div>

                <div class="form-group" style="margin-bottom:15px">
                    <label class="form-label">* Email</label>
                    <input type="text" id="party_email" class="form-control"
                           placeholder="Enter valid email address">
                </div>
                <div class="form-group">
                            <label>Address *</label>
                            <textarea id="address">
                </textarea>
                </div>
                <div class="form-group" style="margin-bottom:20px">
                    <label class="form-label">* Establishment</label>
                    <div style="margin-top:6px">
                        <label style="margin-right:20px;font-weight:500">
                            <input type="radio" name="establishment" value="P">
                            RHC Jodhpur
                        </label>

                        <label style="font-weight:500">
                            <input type="radio" name="establishment" value="B">
                            RHCB Jaipur
                        </label>
                    </div>
                </div>

                <div style="
                    display:flex;
                    gap:12px;
                    justify-content:flex-start;
                    border-top:1px solid #e5e7eb;
                    padding-top:18px;
                    margin-top:20px;
                ">
                    <button type="submit" class="generate-btn">
                        Register Party
                    </button>

                    <button type="button"
                            class="generate-btn"
                            style="background:#6b7280"
                            onclick="location.reload()">
                        Cancel
                    </button>
                </div>

            </form>
        </div>
    `;
    }
</script>