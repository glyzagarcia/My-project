		</div> <!-- jumbotron -->
      <hr>

      <footer>
        <p>&copy;<?php echo date('Y')." | ".COMPANY;?></p>
      </footer>
    </div> <!-- /container -->    
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/bootstrap.min.js"></script>
    
    <!-- Aloha WYSWYG from editor.php -->
  	<!-- load the jQuery and require.js libraries -->
	<script>
		Aloha = {};
		Aloha.settings = { sidebar: { disabled: true } };
	</script>
	<script type="text/javascript" src="js/aloha/require.js"></script>
	<!-- load the Aloha Editor core and some plugins -->
	<script src="js/aloha/aloha.js"
		data-aloha-plugins="common/ui,
			common/format,
			common/list,
			common/link,
			common/highlighteditables">
    </script>
 
	<!-- make all elements with class="editable" editable with Aloha Editor -->
	<script type="text/javascript">
		Aloha.ready( function() {
			var $ = Aloha.jQuery;
				$('.editable').aloha();
			});
	</script>
	<!-- End Aloha WYSWYG from editor.php -->
    
  </body>
</html>