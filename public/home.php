<?php
date_default_timezone_set('Asia/Kolkata'); // Set timezone

include('admin/config/config.php');

$metromonyOptions = "";
$result = $conn->query("SELECT id, name FROM metromony WHERE status = 1");
while ($row = $result->fetch_assoc()) {
    $metromonyOptions .= "<option value='{$row['id']}'>{$row['name']}</option>";
}

$countryCodeOptions = "";
$result = $conn->query("SELECT code FROM cuntry_code WHERE status = 1");
while ($row = $result->fetch_assoc()) {
    $countryCodeOptions .= "<option value='{$row['code']}'>{$row['code']}</option>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $metromony_id = $_POST['metromony'];
    $user_name = $_POST['user_name'];
    $code = $_POST['code'];
    $mobile = $_POST['mobile'];
    $current_time = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO metromony_register (profile, user_name, code, mobile, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $metromony_id, $user_name, $code, $mobile, $current_time, $current_time);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

$conn->close();
?>

<?php include('include/head.php') ?>

<!-- Hero Section -->
<section class="hero-section position-relative" id="register" style="background-image: url('assets/images/banner.png');">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <!-- Left Side: Form -->
            <div class="col-md-5 hero-content-md">
                <div class="form-container mx-auto" style="background: #003366;">

                    <h5 class="text-center mt-3 mb-4" style="padding-top: 20px;">Create a Matrimony Profile</h5>
                   <div class="form-container " style="background: #ffffff;">
            <h6 class="m fw-bold text-dark " style="padding-top: 30px;">Find your perfect match</h6>

         <form action="" method="POST" style="padding: 15px 30px;">
            <div class="mb-4">
                <select name="metromony" class="form-control custom-select fborder" required>
                    <option value="">Select Matrimony</option>
                    <?= $metromonyOptions ?>
                </select>
            </div>

            <div class="mb-4">
                <input type="text" name="user_name" class="form-control fborder" placeholder="Enter Name" required>
            </div>

            <div class="mb-2 d-flex">
                <select name="code" class="form-control w-25 me-2 custom-select fborder" required>
                    <!-- <option value="">Code</option> -->
                    <?= $countryCodeOptions ?>
                </select>
                <input type="text" name="mobile" class="form-control fborder" placeholder="Enter Mobile Number" required pattern="^[0-9]{10}$" title="Please enter a valid 10-digit mobile number">
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2 registerbtn">REGISTER NOW</button>
        </form>

        <p class="small-text text-dark" style="font-size: 13px;">
            *By clicking register free, I agree to the
            <a href="terms.php" class="text-decoration-none">T&C</a> and
            <a href="privacy.php" class="text-decoration-none">Privacy Policy</a>
        </p>
                </div>
            </div>
        </div>

            <div class="col-md-7 text-center">
                <h2 class="fw-bold">Where Traditions Meet Technology for Perfect Matches!</h2>
                <p class="lead">Join our platform and find your perfect match with verified profiles and trusted matchmaking services.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="container text-center my-5" id="home">
    <h3>What You Get</h3>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="star-box">
                <i class="fas fa-user"></i>
            </div>
            <b>Unlimited Matches Recommendations</b>
            <p>Thousands of profiles to explore and find your perfect match.</p>
        </div>
        <div class="col-md-4">
            <div class="star-box">
                <i class="fa fa-check" aria-hidden="true"></i>
            </div>
            <b>Personally Verified</b>
            <p>All profiles go through a verification process to ensure authenticity.</p>
        </div>
        <div class="col-md-4">
            <div class="star-box">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <b>100% Privacy</b>
            <p>Your personal information remains secure and private.</p>
        </div>
    </div>
</section>

<!-- Marriage Stories -->
<section class="container text-center my-5" id="story">
    <h3>Marriage Stories That Inspire</h3>
    <div class="marriage-stories mt-4">
        <img src="assets/images/stories1.png" alt="Story 1">
        <img src="assets/images/stories2.png" alt="Story 2">
        <img src="assets/images/stories3.png" alt="Story 3">
        <img src="assets/images/stories4.jpeg" alt="Story 8">
    </div>
</section>

<style>
        .hero-sections {
            position: relative;
            background: url('assets/images/background.png') center/cover no-repeat;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .overlays {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgb(0 0 0 / 78%);
        }

        .hero-contents {
            position: relative;
            color: white;
            text-align: center;
            max-width: 90%;
            padding: 60px;
            background: rgb(255 255 255 / 32%);
        }
    </style>

    <section class="hero-sections" id="about">
        <div class="overlays"></div>
        <div class="hero-contents">
            <p>At Sheegravivaham, we believe that finding a life partner should be a smooth and joyful journey. Our platform is designed to connect like-minded individuals looking for meaningful and lifelong relationships. With personally verified profiles, unlimited match recommendations, and a strong commitment to 100% privacy, we ensure a seamless journey toward a happy and fulfilling marriage.</p>
            <!--<hp>At Sheegravivaham, we believe that finding a life partner should be a smooth and joyful journey.</p>-->
            <!--<p>Our platform is designed to connect like-minded individuals looking for meaningful and lifelong relationships.</p>-->
            <!--<p>With personally verified profiles, unlimited match recommendations, and a strong commitment to 100% privacy, we ensure a seamless journey toward a happy and fulfilling marriage.</p>-->
        </div>
    </section>
    <section class="mt-5">
        <div class="container">
        <h5 class="text-red">Why Choose Sheegravivaham?</h5>
        <div class="mt-5">
        <b>Quick & Efficient Matchmaking </b>– <span>We help you find the right partner with advanced compatibility filters.</span>
        <br><br><br>
        <b>Verified Profiles –</b><span> Ensuring authenticity and safety for a trustworthy experience.</span>
        <br><br><br>
        <b>User-Friendly Interface – </b><span> Simple and hassle-free navigation for a seamless matchmaking experience.</span>
        <br><br><br>
        <b>Privacy & Security – </b><span>Your personal data is safe with us, with strict privacy controls.</span><br>
        </div>
        </div>
    </section>

    <section>
        <div class="container mt-5">
            <p class="our-mission">Our mission is to bring people together and create happy marriages with trust, tradition, and technology. Whether you're looking for a
        traditional or modern match, Sheegravivaham is here to assist you every step of the way.</p>
        </div>
    </section>





<?php include('include/foot.php') ?>
