<?php

include 'header.php';


?>
<link rel="stylesheet" href="login.css">


<div class="container">
  
  <div class="row" id="pwd-container">
    <div class="col-md-4"></div>
    
    <div class="col-md-4">
      <section class="login-form">
        <form method="post" action="proseslogin.php" role="login">
          
          <h3><center> SILAKAN MASUK </center></h3>
          <input type="text" name="username" placeholder="Username" autocomplete="off" required class="form-control input-lg" value="" />
          
          <input type="password" name="password" class="form-control input-lg" id="password" placeholder="Password  " required="" />
          
          
          
          
          <button type="submit" name="go" style="background-color: #0d47a1" class="btn btn-lg btn-block">Login</button>
                    
        </form>
        
        <div class="form-links">
         
        </div>
      </section>  
      </div>
      
      <div class="col-md-4"></div>
      

  </div>
   
  
  
</div>



<?php

include 'footer.php';

?>