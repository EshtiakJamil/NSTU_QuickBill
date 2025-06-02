<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Bill Table</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 20px;
        }
        .table-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table thead th {
            background-color: #17a2b8;
            color: #ffffff;
            text-align: center;
        }
        .table tfoot td {
            font-weight: bold;
        }
        .header-title {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="table-container">
        <h3 class="header-title">বিল বিবরণী</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-left">বিবরণ</th>
                    <th class="text-center">পরিমাণ</th>
                    <th class="text-right">টাকা</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3"><strong>১. প্রশ্নপত্র মূল্যঃ</strong></td>
                </tr>
                <tr>
                    <td>ক. প্রতি ৬ ঘণ্টার পরীক্ষার Information Security বিষয় CSE 2206 কোর্স কোড</td>
                    <td class="text-center">1</td>
                    <td class="text-right">1,350</td>
                </tr>
                <tr>
                    <td>খ. প্রতি ৬ ঘণ্টার পরীক্ষার Software Requirement Specification & Analysis বিষয় SE 2209 কোর্স</td>
                    <td class="text-center">1</td>
                    <td class="text-right">1,350</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>২. প্রশ্নপত্র সমন্বয়করণ/সহায়কঃ</strong></td>
                </tr>
                <tr>
                    <td>Information Security Lab CSE 2201, CSE 2206, CSE 2207, SE 2209</td>
                    <td class="text-center">-</td>
                    <td class="text-right">2,000</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>৩. স্টাফ স্টেশ / স্টাফ মূল্যায়নঃ</strong></td>
                </tr>
                <tr>
                    <td>SE 2209: Software Requirement Specification & Analysis</td>
                    <td class="text-center">33 × 3</td>
                    <td class="text-right">3,960</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>৪. উত্তরপত্র মূল্যায়নঃ</strong></td>
                </tr>
                <tr>
                    <td>ক. 33 টি ২ ঘণ্টার পরীক্ষার Information Security বিষয় CSE 2206 কোর্স কোড</td>
                    <td class="text-center">33 × 90</td>
                    <td class="text-right">2,970</td>
                </tr>
                <tr>
                    <td>খ. 36 টি ২ ঘণ্টার পরীক্ষার Software Requirement Specification & Analysis বিষয় SE 2209</td>
                    <td class="text-center">36 × 90</td>
                    <td class="text-right">3,240</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>৫. ব্যবহারিক পরীক্ষাঃ</strong></td>
                </tr>
                <tr>
                    <td>ক. 2020 ফাল - Y2-T2 Software Requirement Specification & Analysis Lab বিষয়ে ১ দিন</td>
                    <td class="text-center">3 × 1200</td>
                    <td class="text-right">3,600</td>
                </tr>
                <tr>
                    <td>খ. 2020 ফাল - Y2-T2 Information Security Lab বিষয়ে ১ দিন</td>
                    <td class="text-center">3 × 1200</td>
                    <td class="text-right">3,600</td>
                </tr>
                <tr>
                    <td>গ. 2020 ফাল - Y2-T2 Database Management System - 1 Lab বিষয়ে ১ দিন</td>
                    <td class="text-center">3 × 1200</td>
                    <td class="text-right">3,600</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>৬. মৌখিক পরীক্ষাঃ</strong></td>
                </tr>
                <tr>
                    <td>কোনো তথ্য নেই</td>
                    <td class="text-center">N/A</td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>৭. পরীক্ষা কেন্দ্র পরিচালন ব্যয়ঃ</strong></td>
                </tr>
                <tr>
                    <td>34 টি পরীক্ষার জন্য</td>
                    <td class="text-center">34 × 50</td>
                    <td class="text-right">1,700</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>৮. মডারেটর ও পরীক্ষক সম্মানী / সভাসদ</strong></td>
                </tr>
                <tr>
                    <td>ডাকমাশুল ও অন্যান্য ব্যয়</td>
                    <td class="text-center">-</td>
                    <td class="text-right">2,500</td>
                </tr>
                <tr>
                    <td>ফাইনাল পরীক্ষার প্রতিটি (CSE 2207)</td>
                    <td class="text-center">-</td>
                    <td class="text-right">200</td>
                </tr>
                <tr class="font-weight-bold">
                    <td colspan="2" class="text-right">মোট টাকা:</td>
                    <td class="text-right">30,070</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
