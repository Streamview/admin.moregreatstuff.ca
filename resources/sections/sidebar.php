<?php
?>
  	<!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-dropdown"><a href="#">Navigation</a></div>

        <!--- Sidebar navigation -->
        <!-- If the main navigation has sub navigation, then add the class "has_sub" to "li" of main navigation. -->
        <ul id="nav">
          <!-- Main menu with font awesome icon -->
          <li class="open"><a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
            <!-- Sub menu markup 
            <ul>
              <li><a href="#">Submenu #1</a></li>
              <li><a href="#">Submenu #2</a></li>
              <li><a href="#">Submenu #3</a></li>
            </ul>-->
          </li>
          <li class="has_sub">
			<a href="#"><i class="fa fa-list-alt"></i> Products  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
            <ul>
              <li><a href="listproducts.php">List Products</a></li>
              <li><a href="addproduct.php">Add Product</a></li>
            </ul>
          </li> 
          <!-- <li class="has_sub">
			<a href="#"><i class="fa fa-list-alt"></i> Widgets  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
            <ul>
              <li><a href="widgets1.php">Widgets #1</a></li>
              <li><a href="widgets2.php">Widgets #2</a></li>
              <li><a href="widgets3.php">Widgets #3</a></li>
            </ul>
          </li>  
          <li class="has_sub">
			<a href="#"><i class="fa fa-file-o"></i> Pages #1  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
            <ul>
              <li><a href="post.php">Post</a></li>
              <li><a href="login.php">Login</a></li>
              <li><a href="register.php">Register</a></li>
              <li><a href="support.php">Support</a></li>
              <li><a href="invoice.php">Invoice</a></li>
              <li><a href="gallery.php">Gallery</a></li>
            </ul>
          </li> 
          <li class="has_sub">
			<a href="#"><i class="fa fa-file-o"></i> Pages #2  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
            <ul>
              <li><a href="media.php">Media</a></li>
              <li><a href="statement.php">Statement</a></li>
              <li><a href="error.php">Error</a></li>
              <li><a href="error-log.php">Error Log</a></li>
              <li><a href="calendar.php">Calendar</a></li>
              <li><a href="grid.php">Grid</a></li>
            </ul>
          </li>    
		  <li class="has_sub"><a href="#"><i class="fa fa-table"></i> Tables  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
            <ul>
              <li><a href="tables.php">Tables</a></li>
              <li><a href="dynamic-tables.php">Dynamic Tables</a></li>
            </ul>
          </li>		  
          <li><a href="charts.php"><i class="fa fa-bar-chart-o"></i> Charts</a></li> 
          <li><a href="forms.php"><i class="fa fa-tasks"></i> Forms</a></li>
          <li><a href="ui.php"><i class="fa fa-magic"></i> User Interface</a></li> -->
        </ul>
    </div>
    <!-- Sidebar ends -->