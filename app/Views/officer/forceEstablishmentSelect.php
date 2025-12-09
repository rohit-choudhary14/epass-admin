<div class="modal show" style="display:block; background:rgba(0,0,0,0.6);">
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
                location.reload(); // dashboard will open normally now
            } else {
                alert("Error: " + res.message);
            }
        }, "json"
    );
});
</script>
