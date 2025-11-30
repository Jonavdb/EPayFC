<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Browse Items</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <h2>Item data</h2>
  <li><b>title:</b> <?= $item->title ?> </li>
  <li><b>owner:</b> <?= $item->owner_name ?> </li>
  <li><b>image:</b> <img src="<?php echo $main_image_url;?>" width="500px" height="500px" alt="Item image"> </li>
  <li><b>image count:</b> <?= $item->image_count ?> </li>
  <li><b>price:</b> <?= $item->price ?> </li>
  <li><b>current bid:</b> <?= $item->current_bid ?> </li>
  <li><b>created at:</b> <?=  $item->created_at->format("d-m-Y H:i:s") ?> </li>
  <li><b>duration days:</b> <?= $item->duration_days ?> </li>
  <li>---</li>
  <li><b>has a bidder ?</b> <?= $item->has_bidder ? "yes" : "no" ?> </li>
  <li><b>is in auction ?</b> <?= $item->is_in_auction ? "yes" : "no" ?></li>
  <li><b>can buy now ?</b> <?= $item->current_bid < $item->price  ? "yes" : "no" ?> </li>
  <li><b>more pictures ?</b> <?= $item->image_count > 0 ? "yes, ".$item->image_count." more" : "no" ?></li>
  <li><b>time left :</b>
      <?php
          $time_left = (new DateTime("now"))->diff($item->created_at);   
          $days = $time_left->days;
          $hours = $time_left->h;
          echo $days."d ".$hours."h left";
      ?> 
  </li>

  <!-- GRID LAYOUT -->
  <div class="container">
    <div class="card" style="width: 18rem;">
      <!-- TOP CELL  -->
      <div class="row">
        <div class="col-sm">  
          <!-- we set background img --> 
          <img class="card-img-top" src="uploads/items/1/25cdaf4224c6841a_1762786661_thumbnail.jpg" alt="Card image"">
        

          <!-- we set an overlay (to place elements on top of the bg image) -->
          <div class="card-img-overlay" style="height:50%;">
            <div class="d-flex flex-column h-100">
              <div class="d-flex justify-content-between">

                <!-- top left -->
                <div class="p-2 bg-info">TL</div>

                <!-- top right -->
                 <div class="p-2 bg-info">
                    <div class="d-flex flex-column">
                      <div>TR1</div>
                      <div>TR2</div>
                    </div>
                 </div>
              </div>

              <!-- bottom right -->
              <div class="d-flex justify-content-end mt-auto">
                <div class="p-2 bg-warning">BR</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- BOTTOM CELL -->
      <div class="row">
        <div class="col-sm">
          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>
  </div>  
</div>

</body>

</html>