
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="modal fade" id="estModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px;">

      <div class="modal-header" style="justify-content:center;">
        <h4 class="modal-title" style="font-weight:700; color:#1e40af;">
            Select Establishment
        </h4>
      </div>

      <div class="modal-body" style="text-align:center; font-size:18px;">
        <label style="margin-right:20px;">
          <input type="radio" name="est" value="P"> Principal Seat at Jodhpur
        </label>

        <label>
          <input type="radio" name="est" value="B"> Bench at Jaipur
        </label>
      </div>

      <div class="modal-footer" style="justify-content:center;">
        <button id="saveEstBtn" class="btn btn-primary">Continue</button>
      </div>

    </div>
  </div>
</div>
<script>
$(document).ready(function () {

    <?php if (
        isset($_SESSION['admin_user']) &&
        $_SESSION['admin_user']['role_id'] == 10 && 
        empty($_SESSION['admin_user']['establishment'])
    ): ?>
        var estModal = new bootstrap.Modal(
            document.getElementById('estModal'),
            {
                backdrop: 'static',   
                keyboard: false      
            }
        );
        estModal.show();
        console.log(typeof bootstrap);
    <?php endif; ?>

});
</script>



<script>
$("#saveEstBtn").click(function() {

    let est = $("input[name='est']:checked").val();

    if (!est) {
        alert("Please select an establishment.");
        return;
    }

    $.post("/HC-EPASS-MVC/public/index.php?r=officer/saveEstablishment",
        { establishment: est },
        function(res){
            if(res.status === "OK"){
                location.reload(); 
            } else {
                alert("Error: " + res.message);
            }
        }, "json"
    );
});
</script>
