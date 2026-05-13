<?php
$ph = App\Models\ProposalHarga::find(5);
if ($ph) {
    $ph->approval_step = 3;
    $ph->save();
    echo "Updated PH 5 to step 3\n";
} else {
    echo "PH 5 not found\n";
}
