<html>
    <!-- Variable not actually used anywhere but needs to be set for sidebar to function -->
    <?php $page = "404" ?>
<head>
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="css/global_layout.css" type="text/css">
</head>
<body>
    <?php include 'templates/header_and_sidebar.html';?>
    <content>
        <h2>404 - Page Not Found</h2></br>
            <h3> Oops, looks like the page you're looking for isn't here</h3> </br>
            <h3> Use the sidebar to go to any page that you'd like</h3>

    </content>
    <?php include 'templates/footer.html'; ?>
</body>
</html>
<!-- 404 page displayed when a page is not found - this file has been added to the .httaccess file to redirect here -->
