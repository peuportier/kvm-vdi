<?php
/*
KVM-VDI
Tadas Ustinavičius
tadas at ring.lt
2016-04-21
Vilnius, Lithuania.
*/
include ('functions/config.php');
require_once('functions/functions.php');
if (!check_session()){
    header ("Location: $serviceurl/?error=1");
    exit;
}
$vm=addslashes($_GET['vm']);
$hypervisor=addslashes($_GET['hypervisor']);
$action=addslashes($_GET['action']);
if (empty($vm)||empty($hypervisor)){
    exit;
}
$h_reply=get_SQL_line("SELECT * FROM hypervisors WHERE id='$hypervisor'");
ssh_connect($h_reply[2].":".$h_reply[3]);
$v_reply=get_SQL_line("SELECT name FROM vms WHERE id='$vm'");
$source_path=str_replace("\n", "",(ssh_command("sudo virsh domblklist " . $v_reply[0] . "|grep vda| awk '{print $2}' ",true)));
if (!empty($source_path)){
    ssh_command("sudo virsh destroy " . $v_reply[0], true);
    ssh_command("sudo virsh undefine " . $v_reply[0], true);
    ssh_command("sudo rm " . $source_path, true);
    add_SQL_line("DELETE FROM vms WHERE id='$vm' LIMIT 1");
}
header("Location: $serviceurl/reload_vm_info.php");
exit;
?>
