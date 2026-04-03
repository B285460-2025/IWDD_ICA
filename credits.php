<?php $page = 'credits';?>
<html>
<head>
    <title>Credits - Protein Explorer</title>
    <link rel="stylesheet" href="css/global_layout.css" type="text/css">
</head>
<body>
    <?php include 'templates/header_and_sidebar.html';?>
    <content>
        <h2>Statement of Credits</h2>

        <p>
            External tools and resources were used in the creation of this website, details are shown below.
        </p>

        <h3>External web resources</h3>
        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h4>W3Schools</h4>
            <ul>
                <li><a href="https://www.w3schools.com/css/tryit.asp?filename=trycss_grid_layout_named" target="_blank">CSS Grid Layout</a> — Used as a guide to structure the global css layout for sidebar, header, footer positioning.</li>
                <li><a href="https://www.w3schools.com/howto/howto_js_tabs.asp" target="_blank">How TO - Tabs</a> — Used as a guide to strucutre the pepstats portion of the results page.</li>
                <li><a href="https://www.w3schools.com/html/tryit.asp?filename=tryhtml_table_hover" target="_blank">HTML Table Hover</a> — Adapted to include the css with custom colours to style tables in results and previous searches pages. </li>
                <li><a href="https://www.w3schools.com/php/php_superglobals_post.asp" target="_blank">PHP $_POST Superglobal</a> — Used to understand how to submit form data.</li>
                <li><a href="https://www.w3schools.com/cssref/sel_hover.php" target="_blank">CSS :hover Pseudo-class</a> - Used for sidebar text styling.</li>
            </ul></br>
        </div>

        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h4>StackOverFlow</h4>
                <li><a href="https://stackoverflow.com/questions/381265/better-way-to-check-variable-for-null-or-empty-string" target="_blank">Better way to check variable for null or empty string?</a> — Used to create custom function to check if a string is empty</li>
                <li><a href="https://stackoverflow.com/questions/5946114/how-can-i-replace-newline-or-r-n-with-br" target="_blank">How can I replace newline or \r\n with br >?</a> — Used to find the nl2br function which converts /n in python output to line breaks </li>
                <li><a href="https://stackoverflow.com/questions/5319638/tr-onclick-not-working" target="_blank"> tr onClick not working</a> — Used to understand how to make a whole row in a table a clickable link</li>
                <li><a href="https://stackoverflow.com/questions/52225675/submit-button-not-changing-colour-on-hover" target="_blank">submit button not changing colour on hover</a> — Adapted soltion to create buttons that change colour when hovered over</li>
            </ul>
        </div>

        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
        <h4>Medium</h4>
        <p><a href="https://medium.com/stackanatomy/creating-a-custom-404-error-page-with-html-and-css-a94def4861f7" target="_blank">Creating a custom 404 error page with HTML and CSS</a> - Used to create basic 404 page and .httaccess file</p>
        </div>

        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h4>JetBrains Mono Font</h4>
            <ul>
                <li><a href="https://fonts.googleapis.com/css2?family=JetBrains+Mono&display=swap" target="_blank">Google Fonts - JetBrains Mono</a> — Font used in this site (import is defined in the header template file)</li>
            </ul>
        </div>
        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h4>The following resources were used to understand the commands needed to run analysis tools</h4>
            <ul>
                <li><a href="http://emboss.open-bio.org/rel/dev/apps/plotcon.html" target="_blank">EMBOSS PlotCon</a>
                <li><a href="https://emboss.bioinformatics.nl/cgi-bin/emboss/help/patmatmotifs" target="_blank">Patmatmotifs</a>
                <li><a href="http://emboss.open-bio.org/rel/dev/apps/pepstats.html" target="_blank">Pepstats</a>
            </ul>
        </div>

        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h4>Images</h4>
            <ul>
                <li><a href="https://brand.github.com/foundations/logo" target="_blank">GitHub Logo</a> — The Github Image in the sidebar was obtained from here</li>
            </ul>
        </div>

        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h3>AI/ML</h3>

                <p>
                    Anthropic's Claude Sonnet 4.6 was used for the following:
                </p>
                <ul>
                    <li><b>For each loop (search.php: line 159)</b> — This for each loop was for a nested dictionary where we needed the key and value, AI was used to generate this statement</li>
                    <li><b>CSS Grid layout structure</b> — Used W3 schools as a starting point and used AI to troubleshoot and fix issues with the grid layout within the glocal_layout.css file</li>
                    <li><b>Pepstats tab layout in results</b> — Again, used W3 schools as a starting point and used AI to troubleshoot and fix issues with the tab layout</li>
                    <li><b>Colour Scheme</b> — AI was used to formulate the colour scheme for this website, I originally had a dark blue and white colour scheme but asked AI to create a soft purple colour scheme</li>
                    <li><b>Links withot styling</b> - AI was used to help understand how to have a link without the purple text or underlining. This was for the link in the header and links on the home page</li>
                    <li><b>Sidebar text layout</b> - AI was used to generate the styling for the positioning of the sidebar text in the global_layout.css file and the inline styling of the sidebar text in header_and_sidebar.html</li>
                    <li><b>Positioning of Github logo</b> - I was having issues with positioning the github logo so it would always be at the bottom of my footer. AI suggested adding the poition:relative statement in the global_css for the sidebar and then
                    adding a position:absolute statement to the inline styling for the image in the header_and_sidebar.html file</li>
                </ul>
        </div>
    </content>
    <?php include 'templates/footer.html'; ?>
</body>
</html>
