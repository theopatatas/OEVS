<?php 
include('session.php');
include('dbcon.php');
include('header.php');
?>
<link rel="stylesheet" type="text/css" href="admin/css/style.css" />
<script src="jquery.iphone-switch.js" type="text/javascript"></script>
</head>
<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand">
                <img src="admin/images/au.png" width="60" height="60">
            </a>
            <a class="brand">
                <h2 style="color: white; transition: color 0.3s;" onmouseover="this.style.color='#d5b60a'" onmouseout="this.style.color='white'">ONLINE ELECTION VOTING SYSTEM</h2>
                <div class="chmsc_nav" style="font-size: 18px; color: #fff; transition: color 0.3s; cursor: pointer;" onmouseover="this.style.color='#ffd400'" onmouseout="this.style.color='#fff'">Phinma Araullo University - South</div>
            </a>
            <?php include('head.php'); ?>
        </div>
    </div>
</div>

<div class="wrapper">
    <div class="hero-body-voting">
        <div class="vote_wise" onmouseover="this.style.color='#d5b60a'" onmouseout="this.style.color='white'" style="color: white; font-size: 36px;">
            "Please Vote Wisely"
        </div>

        <div class="help">
            <a class="btn btn-info" id="help" href="complaint_form.php">
                <i class="icon-info-sign icon-large"></i>&nbsp;Complaint Form
            </a>
          
        </div>
    </div>


    <form method="post" action="vote.php">

        <?php
        $positions = [
            'President',
            'Vice-President',
            'Governor',
            'Vice-Governor',
            'Secretary',
            'Treasurer',
            'Social-Media Officer',
            'Representative'
        ];

        foreach ($positions as $position):
            $safe_position = mysqli_real_escape_string($conn, $position);
            $query = mysqli_query($conn, "SELECT * FROM candidate WHERE Position='$safe_position'") or die(mysqli_error($conn));
            if (mysqli_num_rows($query) > 0):
        ?>
        <div class="position-align" style="margin-bottom:30px;">
            <div class="hero-body-candidate">
                <font color="white">Candidate for <?php echo htmlspecialchars($position); ?></font>
            </div>
            <div class="candidates">
                <div class="candidate-margin">
                    <?php while ($row = mysqli_fetch_assoc($query)):
                        $photo = $row['Photo'];
                        // Construct photo URL properly
                        $parts = explode('/', $photo);
                        $filename = array_pop($parts);
                        $folder = implode('/', $parts);
                        $photo_url = 'admin/' . ($folder ? $folder . '/' : 'upload/') . rawurlencode($filename);
                    ?>
                    <label style="
    display: inline-block; 
    text-align: center; 
    margin: 10px; 
    position: relative; 
    color: black; 
    cursor: pointer;
    transition: color 0.3s ease;
" 
onmouseover="this.style.color='#196F38'; this.querySelector('.candidate-photo').style.filter='brightness(0.85)';" 
onmouseout="this.style.color='black'; this.querySelector('.candidate-photo').style.filter='none';"
>
    <img class="candidate-photo" src="<?php echo htmlspecialchars($photo_url); ?>" width="200" height="200" border="0" style="transition: filter 0.3s ease;"><br>
  <input type="radio"
       name="<?php echo strtolower(str_replace(['-', ' '], '_', $position)); ?>"
       value="<?php echo $row['CandidateID']; ?>"
       required
       style="accent-color: #196F38; transition: accent-color 0.3s ease;"
       onmouseover="this.style.accentColor='#196F38';"
       onmouseout="this.style.accentColor='#196F38';"
>

                        <div style="margin-top:5px;"><?php echo htmlspecialchars($row['FirstName'] . " " . $row['LastName']); ?></div>
<div style="margin-top:5px; font-size: 14px; color: #000;"><?php echo htmlspecialchars($row['Party']); ?></div>
                        <a class="btn btn-info btn-small" data-toggle="modal" href="#viewModal<?php echo $row['CandidateID']; ?>" style="margin-top:5px; display: inline-block;">
                            <i class="icon-list icon-large"></i> View
                        </a>
                    </label>

                   <!-- Modal for Candidate Details -->
<div class="modal hide fade" id="viewModal<?php echo $row['CandidateID']; ?>">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Candidate Qualification</h3>
    </div>
    <div class="modal-body" style="text-align:center; background-color:#202c61; color:white;">
        <p><strong>Qualification:</strong> <?php echo htmlspecialchars($row['Qualification']); ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Close</a>
    </div>
</div>


                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php
            endif;
        endforeach;
        ?>

        <div class="thumbnail_widget">
            <div class="submit-vote">
                <button id="save_voter" class="btn btn-success" name="save" type="submit"><i class="icon-thumbs-up icon-large"></i>&nbsp;Submit Vote</button>
            </div>
        </div>

        <div class="thumbnail_widget1">
            <div class="submit-vote">
                <a class="btn" id="index" data-toggle="modal" href="#myModal"><i class="icon-circle-arrow-left icon-large"></i>&nbsp;Vote later</a>
            </div>
        </div>

    </form>
    <br />

    <div class="foot">
        <?php include('footer1.php')?>
    </div>  
</div>

<!-- Modal for Vote Later -->
<div class="modal hide fade" id="myModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3></h3>
    </div>
    <div class="modal-body">
        <p><font color="gray">Are You Sure you Want to Vote Later?</font></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">No</a>
        <a href="logout_back.php" class="btn btn-success">Yes</a>
    </div>
</div>

</body>
</html>
