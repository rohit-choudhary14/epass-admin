<style>
    /* ================= OTP MODAL CSS ================= */

    .otp-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
    }

    .otp-box {
        background: #ffffff;
        padding: 25px 20px;
        border-radius: 8px;
        width: 340px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: otpFadeIn 0.3s ease;
    }

    @keyframes otpFadeIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .otp-title {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .otp-subtitle {
        font-size: 13px;
        color: #555;
        margin: 8px 0 15px;
    }

    .otp-inputs {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 15px;
    }

    .otp-inputs input {
        width: 42px;
        height: 48px;
        font-size: 20px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
    }

    .otp-inputs input:focus {
        border-color: #007bff;
    }

    .otp-timer {
        font-size: 14px;
        color: #d9534f;
        margin-bottom: 15px;
    }

    .otp-actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .btn-verify {
        flex: 1;
        background: #007bff;
        color: #fff;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-verify:hover {
        background: #0056b3;
    }

    .btn-resend {
        flex: 1;
        background: #6c757d;
        color: #fff;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-resend:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
</style>
<div id="otpModal" class="otp-modal">
    <div class="otp-box">
        <h3 class="otp-title">Enter OTP</h3>

        <p class="otp-subtitle">
            OTP has been sent to your mobile number
            <b><span id="otpMobileText"></span></b>

        </p>

        <div class="otp-inputs">
            <input type="text" maxlength="1" oninput="otpInput(this,0)" onkeydown="otpKey(event,0)">
            <input type="text" maxlength="1" oninput="otpInput(this,1)" onkeydown="otpKey(event,1)">
            <input type="text" maxlength="1" oninput="otpInput(this,2)" onkeydown="otpKey(event,2)">
            <input type="text" maxlength="1" oninput="otpInput(this,3)" onkeydown="otpKey(event,3)">
            <input type="text" maxlength="1" oninput="otpInput(this,4)" onkeydown="otpKey(event,4)">
            <input type="text" maxlength="1" oninput="otpInput(this,5)" onkeydown="otpKey(event,5)">
        </div>

        <div class="otp-timer" id="otpTimer">05:00</div>

        <div class="otp-actions">
            <button class="btn-verify" onclick="verifyOtp()">Verify OTP</button>
            <button class="btn-resend" id="resendBtn" onclick="resendOtp()" disabled>
                Resend OTP
            </button>
        </div>
    </div>
</div>


<script>
    /* ========== OTP CONFIG ========== */
    const OTP_CONFIG = {
        length: 6,
        expirySeconds: 300,
        resendLimit: 3
    };

    /* ========== GLOBAL CONTEXT ========== */
    window.OTP_CTX = {
        mobile: null,
        purpose: null,
        role: "ADV",
        payload: null,
        onSuccess: null,
        resendCount: 0
    };

    /* ================= INIT OTP FLOW ================= */
    function initOtpFlow({
        mobile,
        purpose,
        role = "ADV",
        payload,
        onSuccess
    }) {

        if (!mobile) {
            alert("Mobile number required");
            return;
        }

        OTP_CTX.mobile = mobile;
        OTP_CTX.purpose = purpose;
        OTP_CTX.role = role;
        OTP_CTX.payload = payload;
        OTP_CTX.onSuccess = onSuccess;
        OTP_CTX.resendCount = 0;

        sendOtpRequest();
    }

    function maskMobile(mobile) {
        if (!mobile || mobile.length < 10) return mobile;
        return "XXXXXX" + mobile.slice(-4);
    }
    /* ================= SEND OTP ================= */
    async function sendOtpRequest() {

        let fd = new FormData();
        fd.append("mobile", safeEncode(OTP_CTX.mobile));
        fd.append("purpose", OTP_CTX.purpose);
        fd.append("role", OTP_CTX.role);

        showLoader();

        try {
            const response = await fetch("/HC-EPASS-MVC/public/index.php?r=auth/send", {
                method: "POST",
                body: fd
            });

            // ❌ r.json() use nahi karna
            const text = await response.text();

            let res;
            try {
                res = JSON.parse(text);
            } catch (e) {
                throw new Error("Invalid JSON response");
            }

            hideLoader();

            if (res.status !== "OTP_SENT") {
                showError(res.message || "OTP send failed");
                return;
            }

            openOtpModal();
            startOtpTimer(OTP_CONFIG.expirySeconds);

        } catch (err) {
            hideLoader();
            showError("OTP service unavailable");
            console.error("OTP ERROR:", err);
        }
    }


    /* ================= RESEND OTP ================= */
    function resendOtp() {

        if (OTP_CTX.resendCount >= OTP_CONFIG.resendLimit) {
            alert("OTP resend limit reached");
            return;
        }

        OTP_CTX.resendCount++;
        document.getElementById("resendBtn").disabled = true;

        sendOtpRequest();
    }

    /* ================= OTP INPUT UX ================= */
    function otpInput(el, index) {
        el.value = el.value.replace(/[^0-9]/g, "");

        if (el.value && index < OTP_CONFIG.length - 1) {
            el.nextElementSibling.focus();
        }
    }

    function otpKey(e, index) {
        if (e.key === "Backspace" && !e.target.value && index > 0) {
            e.target.previousElementSibling.focus();
        }
    }

    /* ================= COLLECT OTP ================= */
    function collectOtp() {
        let otp = "";
        document.querySelectorAll(".otp-inputs input").forEach(i => {
            otp += i.value;
        });
        return otp;
    }

    /* ================= VERIFY OTP ================= */
    async function verifyOtp() {

        let otp = collectOtp();

        if (otp.length !== OTP_CONFIG.length) {
            showError("Enter complete 6-digit OTP");
            return;
        }

        let fd = new FormData();
        fd.append("mobile", safeEncode(OTP_CTX.mobile));
        fd.append("otp", safeEncode(otp));
        fd.append("role", OTP_CTX.role);

        showLoader();

        try {
            const response = await fetch("/HC-EPASS-MVC/public/index.php?r=auth/verify", {
                method: "POST",
                body: fd
            });

            const text = await response.text();

            let res;
            try {
                res = JSON.parse(text);
            } catch (e) {
                throw new Error("Invalid JSON response");
            }

            hideLoader();

            if (res.status !== "VERIFIED") {
                showError(res.message || "Invalid OTP");
                return;
            }

            closeOtpModal();

            if (typeof OTP_CTX.onSuccess === "function") {
                OTP_CTX.onSuccess(OTP_CTX.payload);
            }

        } catch (err) {
            hideLoader();
            showError("OTP verification failed");
            console.error("VERIFY OTP ERROR:", err);
        }
    }


    /* ================= TIMER ================= */
    let otpInterval;

    function startOtpTimer(seconds) {
        clearInterval(otpInterval);
        let t = seconds;

        otpInterval = setInterval(() => {
            let m = Math.floor(t / 60);
            let s = t % 60;
            document.getElementById("otpTimer").innerText =
                `${m}:${s < 10 ? "0" + s : s}`;

            if (--t < 0) {
                clearInterval(otpInterval);
                document.getElementById("resendBtn").disabled = false;
            }
        }, 1000);
    }

    /* ================= MODAL ================= */
    function openOtpModal() {
        const modal = document.getElementById("otpModal");
        modal.style.display = "flex";

        // 🔥 show masked mobile
        const txt = document.getElementById("otpMobileText");
        if (txt && OTP_CTX.mobile) {
            txt.innerText = maskMobile(OTP_CTX.mobile);
        }

        document.querySelector(".otp-inputs input").focus();
    }


    function closeOtpModal() {
        document.getElementById("otpModal").style.display = "none";
        document.querySelectorAll(".otp-inputs input").forEach(i => i.value = "");
    }

    /* ================= PASTE FULL OTP SUPPORT ================= */
    document.addEventListener("paste", function(e) {
        if (document.getElementById("otpModal").style.display !== "flex") return;

        let paste = (e.clipboardData || window.clipboardData).getData("text");
        if (!/^\d{6}$/.test(paste)) return;

        let inputs = document.querySelectorAll(".otp-inputs input");
        [...paste].forEach((d, i) => inputs[i].value = d);
        inputs[5].focus();
    });
</script>