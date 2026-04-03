<?php $page = 'context';?>
<html>
<head>
    <title>Context - Protein Explorer</title>
    <link rel="stylesheet" href="css/global_layout.css" type="text/css">
</head>
<body>
    <?php include 'templates/header_and_sidebar.html';?>
    <content>
        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h2>About Protein Explorer</h2>

            <p>
                Protein Explorer is a tool designed to conduct bioinformatics analyses on a set of protein sequences obtained from the
                NCBI database. All you need to do is supply the protein family and taxonomic group of the proeteins you're interested in, and 
                Protein explorer will present you with analysis results from the sequences it finds. </br></br></br>
                
                Protein Explorer is great if you would like to:
                <ul>
                    <li>Find a set of sequences related by family or taxonomic group</b></li>
                    <li>Create a multiple sequence alignment of these sequences and understand conservation between them</li>
                    <li>Identify conserved motifs in these sequences</li>
                    <li>Get a general overview of a range of sequence statistics </li>
                </ul>
            </p></br>
        </div>            

        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h2>Overall process breakdown</h2>

            <h4>1. Sequence Discovery</h4>
            <p>
                You can provide the tool the name of a protein family (or type of protein) and a taxanomic group, the tool 
                will search the NCBI database and find the first 10 complete matching records that match your search criteria.
                Records are retrieved via the Entrez edirect tool.
            </p>

            <h4>2. Multiple Sequence Alignment & Conservation Analysis</h4>
            <p>
                Following the sequence retrieval, ClustalO is used to performa a multiple sequence alignment - the alignment
                file in msf format will be made available for you to download. The alignments are then passed to Plotcon which visualises
                the sequence conservation profile to show a visual representation of conservation of different regions across the aligned sequences
                which can be areas for further research as areas of high conservation may have some sort of functional significance.
                Here are some links if you'd like to read more about <a href="https://pmc.ncbi.nlm.nih.gov/articles/PMC3261699/" target="_blank">ClustlO</a> or 
                <a href="http://emboss.open-bio.org/rel/dev/apps/plotcon.html" target="_blank">Plotcon</a>.
            </p>

            <h4>3. Motif Analysis</h4>
            <p>
                For each sequence in the analysis, the protein sequence is searched against the PROSITE database for motifs. Any motifs found
                may be used to help identify the protein function as they may be indiciative of bindings sites. You can read a little more about 
                the tool used for motif finding <a href="https://emboss.bioinformatics.nl/cgi-bin/emboss/help/patmatmotifs" target="_blank">here</a>.
            </p>

            <h4>5. Pepstats Analysis </h4>
            <p>
                For each sequence found, pepstats is used to  calculate sequence properties such as amino acid composition, molecular weight,
                isoelectric point, and more. These properties can be used to predict protein functionality, and inform structural stability.
            </p></br>
        </div>

         <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h2>Limitations</h2>
            <ul>
                <li><b>Limited sequence searching:</b> The tool currently returns only a maximum of 10 records from the NCBI database, in future it could be updated to allow this to be user defined</li>
                <li><b>No custom analysis:</b> At present the tool doesn't allow users to input their own sequences for analysis, it could be updated to allow this in future </li>
                <li><b>Computational time:</b> It may take a few minutes to process results where the data sets contain large sequences</li>
            </ul>
        </div>

    </content>
    <?php include 'templates/footer.html'; ?>
</body>
</html>
