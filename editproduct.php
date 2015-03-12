<?php include 'resources/sections/header.php';?>
<?php 
$category_list = new Models\ProductCategoryList;
$category_list->findAll();

$product = null;
if (!empty($_REQUEST['id'])) {
	$product = Models\Product::findByid($_REQUEST['id']);
	if (empty($product)) {
		header("Location: /listproducts.php");
	}
}
?>
<!-- Header ends -->

<!-- Main content starts -->

<div class="content">

  	<!-- Sidebar -->
    <?php include 'resources/sections/sidebar.php';?>

    <!-- Sidebar ends -->

  	<!-- Main bar -->
  	<div class="mainbar">
      
	    <!-- Page heading -->
	    <div class="page-head">
        <!-- Page heading -->
	      <h2 class="pull-left"><i class="fa fa-file-o"></i> Add Product</h2>
        </h2>


        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right">
          <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <span class="divider">/</span> 
          <a href="index.html"><i class="fa fa-home"></i> Products</a> 
          <span class="divider">/</span> 
          <a href="#" class="bread-current">Add Product</a>
        </div>
        <div class="clearfix"></div>

	    </div>
	    <!-- Page heading ends -->



	    <!-- Matter -->

	    <div class="matter">
        <div class="container">

          <div class="row">

            <div class="col-md-12">


              <div class="widget wgreen">
                
                <div class="widget-head">
                  <div class="pull-left"><?php if(!empty($product)) { ?>Edit Product<?php } else { ?>Add Product<?php }?></div>
                  <div class="widget-icons pull-right">
                    <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 
                    <a href="#" class="wclose"><i class="fa fa-times"></i></a>
                  </div>
                  <div class="clearfix"></div>
                </div>

                <div class="widget-content">
                  <div class="padd">

                    <br />
                    <!-- Form starts.  -->
                     <form id="edit-product-form" class="form-horizontal" role="form">
                              	<input type="hidden" name="product_id" value="<?php if(!empty($product)) echo $product->id;?>">
                              	
                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Code</label>
                                  <div class="col-lg-5">
                                    <input type="text" class="form-control" placeholder="Product Code" name="code" value="<?php echo $product->code;?>">
                                  </div>
                                </div>
                                
                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Name</label>
                                  <div class="col-lg-5">
                                    <input type="text" class="form-control" placeholder="Product Name" name="name" value="<?php echo $product->name;?>">
                                  </div>
                                </div>
                                
                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Quantity</label>
                                  <div class="col-lg-5">
                                    <input type="number" min="1" class="form-control" placeholder="Quantity" name="quantity" value="<?php echo $product->quantity;?>">
                                  </div>
                                </div>
                                
                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Price</label>
                                  <div class="col-lg-5">
                                    <input type="text" class="form-control" placeholder="Price" name="price" value="<?php echo $product->price;?>">
                                  </div>
                                </div>
                                
                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Category</label>
                                  <div class="col-lg-2">
                                    <select name="category_id" class="form-control">
                                      <?php foreach ($category_list->objects as $category) { ?>
										<option value="<?php echo $category->id;?>"><?php echo $category->label;?></option>
									<?php }?>
                                    </select>
                                  </div>
                                </div>   
                                
                                
                                
                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Img</label>
                                  <div class="col-lg-5">
                                  	<?php if (!empty($product)) {?>
	                                	<img class="product-img" src="<?php echo $product->getImg()->tmp_path;?>"/><br/>
	                                <?php } ?>
                                    <input type="file" class="form-control" name="img">
                                  </div>
                                </div>
                                
                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Description</label>
                                  <div class="col-lg-5">
                                    <textarea class="form-control" rows="5" placeholder="Description" name="description"><?php echo $product->description;?></textarea>
                                  </div>
                                </div>       

                               
                                <div class="form-group">
                                  <div class="col-lg-offset-2 col-lg-6">
                                    <button id="save-product-btn" type="button" class="btn btn-sm btn-primary" data-loading-text="Saving ...">Save</button>
                                    <a type="button" class="btn btn-sm btn-warning" href="/listproducts.php">Cancel</a>
                                  </div>
                                </div>
                              </form>
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

		<!-- Matter ends -->

    </div>

   <!-- Mainbar ends -->	    	
   <div class="clearfix"></div>

</div>
<!-- Content ends -->
<!-- Footer starts -->
<?php include 'resources/sections/footer.php';?>
<style>
.product-img {
	width:150px;
}
</style>
<script type="text/javascript">
<!--
	
//-->
$(document).ready(function(){
	$("#save-product-btn").on('click', function() {
		var btn = this;
		var company_staff_id = $("#edit-staff-content #company-staff-id").val();
		//$(btn).button('loading');
		
		$("#edit-product-form").ajaxSubmit({
			url:'/resources/scripts/addproduct.php', 
			type:'post',
			dataType:'json',
			success:function (response, statusText, xhr, el) {
				if (response.success) {
					/*app.notySuccess(response.message,{
						callback:{
							afterClose:function () {
								if (response.data.reload) {
									window.location.href = "/Staff/edit/" + response.data.staff_id;
								}
							}
						}
					});*/
					alert(response.message);
					if (response.product_id) {
						window.location.href = "/editproduct.php?id=" + response.product_id;
					}
				} else {
					alert(response.errors[0]);
				}
				//$(btn).button('reset');
			},
			error:function (jqXHR) {
				alert(jqXHR.responseText);
				//$(btn).button('reset');
			},
		});
		return false;
	});
});
</script>