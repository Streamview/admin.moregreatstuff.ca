<?php include 'resources/sections/header.php';?>
<!-- Header ends -->
<?php 
	$product_list = new Models\ProductList;
	$product_list->findByCriteria(array(
		"status" => "active"
	));

?>


<!-- Main content starts -->
<div class="content">

  	<!-- Sidebar -->
    <?php include 'resources/sections/sidebar.php';?>
    <!-- Sidebar ends -->

  	<!-- Main bar -->
  	<div class="mainbar">

      <!-- Page heading -->
      <div class="page-head">
        <h2 class="pull-left"><i class="fa fa-table"></i> List Products</h2>

        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right">
          <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <span class="divider">/</span> 
          <a href="index.html"><i class="fa fa-home"></i> Products</a> 
          <span class="divider">/</span> 
          <a href="#" class="bread-current">List Products</a>
        </div>

        <div class="clearfix"></div>

      </div>
      <!-- Page heading ends -->


	    <!-- Matter -->

	    <div class="matter">
        <div class="container">
          <div class="row">
            <div class="col-md-12">

              <div class="widget">
                <div class="widget-head">
                  <div class="pull-left">Data Tables</div>
                  <div class="widget-icons pull-right">
                    <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 
                    <a href="#" class="wclose"><i class="fa fa-times"></i></a>
                  </div>  
                  <div class="clearfix"></div>
                </div>
                <div class="widget-content">
                  <div class="padd">
                    
							<!-- Table Page -->
							<div class="page-tables">
								<!-- Table -->
								<div class="table-responsive">
									<table cellpadding="0" cellspacing="0" border="0" id="data-table-1" width="100%">
										<thead>
											<tr>
												<th>ID</th>
												<th>Code</th>
												<th>Name</th>
												<th>Img</th>
												<th>Description</th>
												<th>Category</th>
												<th>Quantity</th>
												<th>Price</th>
												<th>Create Date</th>
												<th>Options</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($product_list->objects as $product) {?>
												<tr>
													<td><?php echo $product->subid;?></td>
													<td><?php echo $product->code;?></td>
													<td><?php echo $product->name;?></td>
													<td><img class="product-img" src="<?php echo $product->getImg()->tmp_path;?>"/></td>
													<td><?php echo $product->description;?></td>
													<td><?php echo $product->getCategory()->label;?></td>
													<th><?php echo $product->quantity;?></th>
													<td>$<?php echo $product->price;?></td>
													<td><?php echo $product->create_date;?></td>
													<td>
														<a href="/editproduct.php?id=<?php echo $product->id;?>" class="btn btn-xs btn-primary" title="Edit Product"><i class="fa fa-edit"></i></a>
													</td>
												</tr>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th>ID</th>
												<th>Code</th>
												<th>Name</th>
												<th>Img</th>
												<th>Description</th>
												<th>Category</th>
												<th>Quantity</th>
												<th>Price</th>
												<th>Create Date</th>
												<th>Options</th>
											</tr>
										</tfoot>
									</table>
									<div class="clearfix"></div>
								</div>
								</div>
							</div>

					
                  </div>
                  <div class="widget-foot">
                    <!-- Footer goes here -->
                  </div>
                </div>
              </div>  
              
            </div>
          </div>
        </div>
		  </div>
		<!-- Matter ends -->
    </div>

   <!-- Mainbar ends -->	    	
   <div class="clearfix"></div>

</div>
<!-- Content ends -->
<style>
.product-img {
	width:150px;
}
</style>
<!-- Footer starts -->
<?php include 'resources/sections/footer.php';?>