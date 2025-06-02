<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - NSTU-Quickbill</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            color: black;
        }

        .navbar-custom {
            background-color: #82c0ab;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: black;
            font-weight: bold;
        }

        .navbar-custom .nav-link:hover {
            color: #cccccc;
        }

        .container {
            margin-top: 50px;
        }

        .footer {
            background-color:#061e6d;
            color: rgb(241, 238, 238);
            padding: 10px 0;
            font-size: 14px;
        }

        .footer a {
            color: rgb(255, 245, 245);
        }

        .footer a:hover {
            color: #cccccc;
            text-decoration: none;
        }

        .footer .social-icons a {
            margin: 0 10px;
            color: white;
        }

        .container {
            padding-bottom: 60px;
        }

        .classic-font {
            font-family: 'Playfair Display', serif;
        }

        .contact-details {
            margin-top: 20px;
            padding: 20px;
            background-color: hsl(170, 14%, 84%);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .contact-details h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .contact-form {
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(235, 229, 229, 0.1);
        }

        .contact-form label {
            font-weight: bold;
        }

        .contact-form input[type="text"],
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: none;
        }

        .contact-form input[type="submit"] {
            background-color: #061e6d;
            color: rgb(241, 234, 234);
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .contact-form input[type="submit"]:hover {
            background-color: #1ca79e;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">
            <img src="../images/Logo.png" alt="Logo" style="width: 40px; height: 40px; margin-right: 10px;">
            NSTU-Quickbill
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactus.php"><i class="fas fa-envelope"></i> Contact Us</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 contact-details">
                <h2 class="classic-font">Contact Details</h2>
                <p>Feel free to reach out to us for any queries or feedback!</p>
                <ul>
                    <li><strong>Email:</strong> register@office.nstu.edu.bd</li>
                    <li><strong>Phone:</strong> 02334496522</li>
                    <li><strong>Fax:</strong> 02334496523</li>
                    <li><strong>Address:</strong> Sonapur, Prajhukti Road, Noakhali</li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2 contact-form">
                <h2 class="classic-font">Send us a Message</h2>
                <form action="#" method="post">
                    <div class="form-group">
                        <label for="name">Your Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Your Email:</label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer text-center">
        <div class="container">
            <p>&copy; 2024 NSTU-Quickbill. All rights reserved.</p>
            <p><a href="#">Privacy Policy | Terms of Service</a></p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
