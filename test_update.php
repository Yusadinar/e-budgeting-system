<?php
$ia = App\Models\InternalAgreement::find(5);
if ($ia) {
    $ia->approval_step = 3;
    $ia->save();
    echo "Updated IA 5 to step 3\n";
} else {
    echo "IA 5 not found\n";
}
