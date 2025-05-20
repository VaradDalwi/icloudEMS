<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "assignment_icloudems";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_FILES['csvFile']['name'])) {
    $filename = $_FILES['csvFile']['tmp_name'];

    if ($_FILES['csvFile']['size'] > 0) {
        $file = fopen($filename, "r");
        $header = fgetcsv($file);

        $inserted = 0;
        $sum_due_amount = 0;
        $sum_paid_amount = 0;
        $sum_concession_amount = 0;
        $sum_scholarship_amount = 0;
        $sum_refund_amount = 0;

        while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
            $date=date_create($data[1]);
            $date=date_format($date,"Y-m-d");

            $query = "INSERT INTO `temporary_completedata` (`s_no`, `date`, `academic_year`, `session`, `alloted_category`, `voucher_type`,
                            `voucher_number`, `roll_no`, `adm_no`, `status`, `fee_category`, `faculty`,
                            `program`, `department`, `branch`, `receipt_number`, `fee_head`, `due_amount`,
                            `paid_amount`, `concession_amount`, `scholarship_amount`, `reverse_concession_amount`,
                            `writoff_amount`, `adjusted_amount`, `refund_amount`, `fund_transfer_amount`) 
                            VALUES ('$data[0]', '$date', '$data[2]', '$data[3]', '$data[4]', '$data[5]',
                            '$data[6]', '$data[7]', '$data[8]', '$data[9]', '$data[10]', '$data[11]',
                            '$data[12]', '$data[13]', '$data[14]', '$data[15]', '$data[16]', '$data[17]',
                            '$data[18]', '$data[19]', '$data[20]', '$data[21]', '$data[22]', '$data[23]',
                            '$data[24]', '$data[25]')";

            if (mysqli_query($conn, $query)) {
                $inserted++;
                $sum_due_amount += (float)$data[17];
                $sum_paid_amount += (float)$data[18];
                $sum_concession_amount += (float)$data[19];
                $sum_scholarship_amount += (float)$data[20];
                $sum_refund_amount += (float)$data[24];
            }
            else{
                echo "Error On: ".$data[0]."\n";
            }
        }

        fclose($file);
        
        if($inserted == 884950 && $sum_due_amount == 12654422921 && $sum_paid_amount == 11461021901.4 && $sum_concession_amount == 90544480 && $sum_scholarship_amount == 471818093 && $sum_refund_amount == -173381473){

            $static_query = "INSERT INTO `entry_mode`(`entry_modename`, `crdr`, `entry_mode_no`) VALUES
                                ('DUE', 'D', 0),
                                ('REVDUE', 'C', 12),
                                ('SCHOLARSHIP', 'C', 15),
                                ('REVSCHOLARSHIP/REVCONCESSION', 'D', 16),
                                ('CONCESSION', 'C', 15),
                                ('RCPT', 'C', 0),
                                ('REVRCPT', 'D', 0),
                                ('JV', 'C', 14),
                                ('REVJV', 'D', 14),
                                ('PMT', 'D', 1),
                                ('REVPMT', 'C', 1),
                                ('Fundtransfer', '+ ve and - ve', 1)";
            $result = mysqli_query($conn,$static_query);
            if(!$result){
                echo "Insert into entry_mode Failed \n";
                die();
            }

            $static_query2 = "INSERT INTO `module`(`module_name`, `module_id`) VALUES
                                ('academic', 1),
                                ('academicmisc', 11),
                                ('hostel', 2),
                                ('hostelmisc', 22),
                                ('transport', 3),
                                ('transportmisc', 33)";
            $result2 = mysqli_query($conn,$static_query2);
            if(!$result2){
                echo "Insert into module Failed \n";
                die();
            }

            $sql1 = "INSERT INTO branches (branch_name)
                        SELECT DISTINCT faculty
                        FROM temporary_completedata
                        WHERE faculty IS NOT NULL
                        AND faculty != ''";
            $run1 = mysqli_query($conn,$sql1);
            if(!$run1){
                echo "Insert into Branches Failed \n";
                die();
            }

            $sql2 = "INSERT INTO feecategory (fee_category, br_id)
                        SELECT tc.fee_category,b.id
                        FROM (
                            SELECT DISTINCT fee_category
                            FROM temporary_completedata
                            WHERE fee_category IS NOT NULL AND fee_category != ''
                            ORDER BY fee_category
                            LIMIT 3
                        ) AS tc
                        CROSS JOIN branches b";

            $run2 = mysqli_query($conn,$sql2);
            if(!$run2){
                echo "Insert into Fee Category Failed \n";
                die();
            }

            $sql3 = "INSERT INTO feecollectiontype (collection_head, collection_desc, br_id)
                        SELECT ft.name,ft.name,b.id
                        FROM (
                            SELECT 'academic' AS name
                            UNION ALL
                            SELECT 'academicmisc'
                            UNION ALL
                            SELECT 'hostel'
                            UNION ALL
                            SELECT 'hostelmisc'
                            UNION ALL
                            SELECT 'transport'
                            UNION ALL
                            SELECT 'transportmisc'
                        ) AS ft
                        CROSS JOIN branches b";

            $run3 = mysqli_query($conn,$sql3);
            if(!$run3){
                echo "Insert into Fee Collection Type Failed \n";
                die();
            }

            $sql4 = "INSERT INTO feetypes (f_name, fee_type_ledger, br_id, seq_id)
                        SELECT u.fee_head,u.fee_head,b.id,s.seq_id
                        FROM (
                            SELECT fee_head, ROW_NUMBER() OVER (ORDER BY fee_head) AS seq_id
                            FROM (SELECT DISTINCT fee_head FROM temporary_completedata WHERE fee_head IS NOT NULL AND fee_head != '') AS distinct_heads
                        ) AS s
                        JOIN (SELECT DISTINCT fee_head FROM temporary_completedata WHERE fee_head IS NOT NULL AND fee_head != '') AS u ON u.fee_head = s.fee_head
                        CROSS JOIN branches b";

            $run4 = mysqli_query($conn,$sql4);
            if(!$run4){
                echo "Insert into Fee Type Failed \n";
                die();
            }

            $sql5 = "INSERT INTO `financial_trans`(`trans_id`, `adm_no`, `amount`, `crdr`, `tran_date`, `acad_year`, `entry_mode_no`, `voucher_number`, `br_id`, `type_of_concession`)
                        SELECT UUID(),t.adm_no,SUM(t.due_amount+t.writoff_amount+t.scholarship_amount+t.reverse_concession_amount+t.concession_amount) AS total_amount,e.crdr,t.date,t.academic_year,e.entry_mode_no,t.voucher_number,b.id AS branch_id,
                            CASE
                                WHEN t.concession_amount != 0 THEN 1
                                WHEN t.scholarship_amount != 0 THEN 2
                                ELSE NULL
                            END AS type_of_concession
                        FROM temporary_completedata AS t
                        LEFT JOIN branches AS b ON b.branch_name = t.faculty
                        LEFT JOIN entry_mode AS e ON e.entry_modename = t.voucher_type
                        WHERE t.voucher_type IN ('DUE','REVDUE','SCHOLARSHIP','REVCONCESSION','REVSCHOLARSHIP','CONCESSION')
                        GROUP BY t.voucher_number";

            $run5 = mysqli_query($conn,$sql5);
            if (!$run5) {
                echo "Insert into Financial Trans Failed \n";
                die();
            }

            $sql6 = "INSERT INTO financial_transdetail (`financial_trans_id`, `amount`, `head_id`, `crdr`, `br_id`, `head_name`)
                        SELECT fin.fin_id,t.due_amount+t.writoff_amount+t.scholarship_amount+t.reverse_concession_amount+t.concession_amount,ft.ft_id,e.crdr,b.id,ft.f_name
                        FROM temporary_completedata AS t
                        LEFT JOIN branches AS b ON b.branch_name = t.faculty
                        LEFT JOIN entry_mode AS e ON e.entry_modename = t.voucher_type
                        LEFT JOIN (SELECT MIN(id) AS ft_id, f_name FROM feetypes GROUP BY f_name) AS ft ON ft.f_name = t.fee_head
                        LEFT JOIN (SELECT MIN(id) AS fin_id, voucher_number FROM financial_trans GROUP BY voucher_number) AS fin ON fin.voucher_number = t.voucher_number
                        WHERE t.voucher_type IN ('DUE','REVDUE','SCHOLARSHIP','REVCONCESSION','REVSCHOLARSHIP','CONCESSION');";

            $run6 = mysqli_query($conn,$sql6);
            if (!$run6) {
                echo "Insert into Financial Trans Details Failed \n";
                die();
            }

            $sql7 = "INSERT INTO `common_fee_collection`(`trans_id`, `adm_no`, `roll_no`,amount , `br_id`, `academic_year`, `financial_year`, `display_receipt_no`, `entry_mode`, `paid_date`, `inactive`)
                        SELECT UUID(),t.adm_no,t.roll_no,SUM(t.paid_amount + t.adjusted_amount + t.refund_amount + t.fund_transfer_amount),b.id,t.academic_year,t.session,t.receipt_number,e.entry_mode_no,t.date,
                        CASE
                            WHEN t.voucher_type IN ('RCPT', 'JV', 'PMT') THEN 0
                            WHEN t.voucher_type IN ('REVRCPT', 'REVJV', 'REVPMT') THEN 1
                            WHEN t.voucher_type = 'Fundtransfer' THEN NULL
                        END AS inactive
                    FROM temporary_completedata t
                    LEFT JOIN branches b ON b.branch_name = t.faculty
                    LEFT JOIN (SELECT DISTINCT entry_modename, entry_mode_no FROM entry_mode) e ON e.entry_modename = t.voucher_type
                    WHERE t.voucher_type IN ('RCPT','REVRCPT','JV','REVJV','PMT','REVPMT','Fundtransfer')
        			GROUP BY t.voucher_number,t.adm_no,t.roll_no,b.id,t.academic_year,t.session,t.receipt_number,e.entry_mode_no,t.date,t.receipt_number,t.adm_no,t.voucher_type";

            $run7 = mysqli_query($conn,$sql7);
            if (!$run7) {
                echo "Insert into common_fee_collection Failed \n";
                die();
            }
            
            $sql8 = "INSERT INTO `common_fee_collection_headwise`(`receipt_id`,`head_id`, `head_name`, `br_id`, `amount`)
                        SELECT cfc.cfc_id, ft.ft_id, ft.f_name, b.id,t.paid_amount + t.adjusted_amount + t.refund_amount + t.fund_transfer_amount 
                        FROM temporary_completedata AS t
                        LEFT JOIN (SELECT MIN(id) AS ft_id, f_name FROM feetypes GROUP BY f_name) AS ft ON ft.f_name = t.fee_head
                        LEFT JOIN branches AS b ON b.branch_name = t.faculty
                        LEFT JOIN (SELECT MIN(id) AS cfc_id, display_receipt_no FROM common_fee_collection GROUP BY display_receipt_no) AS cfc ON cfc.display_receipt_no = t.receipt_number
                        WHERE voucher_type IN ('RCPT','REVRCPT','JV','REVJV','PMT','REVPMT','Fundtransfer')";

            $run8 = mysqli_query($conn,$sql8);
            if (!$run8) {
                echo "Insert into common_fee_collection_headwise Failed \n";
                die();
            }  
        
        }
        else{
            echo "Error in Insertion.\n";
        }
    } else {
        echo "Uploaded file is empty.";
    }
} else {
    echo "No file uploaded.";
}
?>
