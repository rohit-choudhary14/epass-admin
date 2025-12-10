</main>
<!-- Change Establishment Modal -->
<div class="modal fade" id="estModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px;">

      <div class="modal-header" style="justify-content:center;">
        <h4 class="modal-title" style="color:#1e40af;font-weight:800;">Select Establishment</h4>
      </div>

      <div class="modal-body" style="text-align:center;font-size:18px;">
        <label style="margin-right:20px;">
          <input type="radio" name="est" value="P"> Principal Seat at Jodhpur
        </label>

        <label>
          <input type="radio" name="est" value="B"> Bench at Jaipur
        </label>
      </div>

      <div class="modal-footer" style="justify-content:center;">
        <button id="saveEstBtn" class="btn btn-primary" style="padding:8px 25px;font-size:18px;">Continue</button>
      </div>

    </div>
  </div>
</div>
<div id="global-msg" 
     style="display:none; position:fixed; top:20px; right:20px; 
            padding:15px 20px; border-radius:8px; color:white; 
            font-weight:600; z-index:99999;">
</div>



</body>
<script>
function showLoader() {
    document.getElementById("global-loader").style.display = "flex";
}

function hideLoader() {
    document.getElementById("global-loader").style.display = "none";
}
</script>

<script>
$(document).ready(function(){

    // Open modal when link clicked
    $("#openEstModal").click(function() {
        var estModal = new bootstrap.Modal(document.getElementById('estModal'));
        estModal.show();
    });

    // Save establishment
    $("#saveEstBtn").click(function() {

        let est = $("input[name='est']:checked").val();

        if (!est) {
            alert("Please select an establishment.");
            return;
        }

        // AJAX request to backend
        $.ajax({
            url: "/HC-EPASS-MVC/public/index.php?r=officer/ch_estab",
            type: "POST",
            data: { establishment: est },
            success: function(res) {
                try {
                    let r = JSON.parse(res);
                    if (r.status === "OK") {
                        alert("Establishment updated successfully!");
                        window.location.href = "/HC-EPASS-MVC/public/index.php?r=officer/dashboard";
                    } else {
                        alert(r.message);
                    }
                } catch(e){
                    alert("Unexpected response");
                }
            }
        });

    });

});
</script>
<script>
function showError(msg) {
    let box = document.getElementById("global-msg");
    box.style.background = "#dc2626"; // red
    box.innerHTML = msg;
    box.style.display = "block";
    setTimeout(() => box.style.display = "none", 3000);
}

function showSuccess(msg) {
    let box = document.getElementById("global-msg");
    box.style.background = "#16a34a"; // green
    box.innerHTML = msg;
    box.style.display = "block";
    setTimeout(() => box.style.display = "none", 3000);
}

function safeEncode(str) {
    return btoa(unescape(encodeURIComponent(str))); 
}
</script>



</html>
