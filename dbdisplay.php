<?php


$conn = mysqli_connect('localhost', 'root', '', 'abhi');


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle completion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['complete_id'])) {
        $completeId = $_POST['complete_id'];

        // Fetch the booking information
        $selectQuery = "SELECT * FROM bookings WHERE id = '$completeId'";
        $bookingResult = mysqli_query($conn, $selectQuery);
        $bookingData = mysqli_fetch_assoc($bookingResult);

        // Insert the booking into completed history
        $insertQuery = "INSERT INTO completed_bookings (package_type,amount, full_name, mobile_no, wash_date, wash_time, message, status)
                        VALUES (
                            '{$bookingData['package_type']}',
                            '{$bookingData['amount']}',
                            '{$bookingData['full_name']}',
                            '{$bookingData['mobile_no']}',
                            '{$bookingData['wash_date']}',
                            '{$bookingData['wash_time']}',
                            '{$bookingData['message']}',
                            'completed'
                        )";
        if (mysqli_query($conn, $insertQuery)) {
            // Delete the booking from the bookings table
            $deleteQuery = "DELETE FROM bookings WHERE id = '$completeId'";
            if (mysqli_query($conn, $deleteQuery)) {
                echo "Booking with ID $completeId has been completed and moved to completed history successfully.";
            } else {
                echo "Error completing booking: " . mysqli_error($conn);
            }
        } else {
            echo "Error completing booking: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['booking_id'])) {
        $bookingId = $_POST['booking_id'];
        $deleteQuery = "DELETE FROM bookings WHERE id = '$bookingId'";
        if (mysqli_query($conn, $deleteQuery)) {
            echo "Booking with ID $bookingId has been canceled successfully.";
        } else {
            echo "Error canceling booking: " . mysqli_error($conn);
        }
    }
}

$query = "SELECT * FROM bookings";
$result = mysqli_query($conn, $query);
$data = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        array_unshift($data, $row);
    }
}
?>

<!-- Your HTML and CSS code here -->



<style>
    .text {
        text-align: center;
        color: black;
        font-size: 15px;
    }

    .head {
        border: 2px solid #ccc;
    }

    .head1 {
        border: 2px solid #ccc;
    }

    .center {
        text-align: center;
        background-color: lightgray;
        font-size: 100px;
    }

    .table-bordered {
        border-collapse: collapse;
        border: 2px solid #ccc;
    }

    .table-bordered th,
    .table-bordered td {
        border: 2px solid #ccc;
        padding: 8px;
    }

    .table-bordered th {
        background-color: lightgray;
    }

    .table-bordered tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .table-bordered tbody tr:hover {
        background-color: #e6e6e6;
    }

    .cancel-btn {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 6px 12px;
        cursor: pointer;
    }

    .complete-btn {
        background-color: #4caf50;
        color: white;
        border: none;
        padding: 6px 12px;
        cursor: pointer;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="center">New Bookings</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col m-auto">
                <div class="card mt-5">



                    <table class="table table-bordered">
                        <thead class="head">
                            <tr>
                                <th class="text">Package Type</th>
                                <th class="text">amount</th>
                                <th class="text">Full Name</th>
                                <th class="text">Mobile No</th>
                                <th class="text">Wash Date</th>
                                <th class="text">Wash Time</th>
                                <th class="text">Message</th>
                                <th class="text">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="head1">
                            <?php foreach ($data as $d) { ?>
                                <tr>
                                    <td class="text"><?php echo $d['package_type']; ?></td>
                                    <td class="text"><?php echo $d['amount']; ?></td>
                                    <td class="text"><?php echo $d['full_name']; ?></td>
                                    <td class="text"><?php echo $d['mobile_no']; ?></td>
                                    <td class="text"><?php echo $d['wash_date']; ?></td>
                                    <td class="text"><?php echo $d['wash_time']; ?></td>
                                    <td class="text"><?php echo $d['message']; ?></td>
                                    <td class="text">
                                        <?php if ($d['status'] !== 'completed') { ?>
                                            <button class="cancel-btn" data-booking-id="<?php echo $d['id']; ?>">Cancel</button>
                                            <button class="complete-btn" data-booking-id="<?php echo $d['id']; ?>">Complete</button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $(".cancel-btn").click(function() {
            var bookingId = $(this).data("booking-id");
            if (confirm("Are you sure you want to cancel this booking?")) {
                $.post("", {
                    booking_id: bookingId
                }, function(response) {
                    alert(response);
                    location.reload(); // Refresh the page after successful cancellation
                }).fail(function() {
                    alert("Error canceling booking.");
                });
            }
        });

        $(".complete-btn").click(function() {
            var completeId = $(this).data("booking-id");
            if (confirm("Are you sure you want to mark this booking as complete?")) {
                $.post("", {
                    complete_id: completeId
                }, function(response) {
                    alert(response);
                    location.reload(); // Refresh the page after successful completion and moving to history
                }).fail(function() {
                    alert("Error completing booking.");
                });
            }
        });
    });
</script>