<?php
function getEnergy($pos){
	$home=dirname(__FILE__);
	shell_exec("cp $pos $home/data.pos");
	$energy=shell_exec("cd $home;~/lmp_ubuntu<in.energy>log.out;tail -1 dump.energy;");
	return $energy+0.0;
}
function getBondEnergy($r){
	
}
function getMinimal($r){
$e0=0;$e1=2;
while(abs($e0-$e1)>1e-5){
	$e0=getBondEnergy($r);
	$e1=getBondEnergy($r+1e-5);
	$tgn=($e1-$e0)/1e-5;
	$r+=-$tgn*1e-4;
	}
}
function getVasprun($POSCAR){
	$home=dirname(__FILE__);
	shell_exec("cp $POSCAR $home/POSCAR");
	get_latgen();
	getpos();
	shell_exec("cd $home;~/lmp_ubuntu <in.graphene");
	get_vasprun();
}
function getpos(){
	$home=dirname(__FILE__);
	// input file of latgen
	$fp=fopen("$home/lat.in","w");
	fprintf($fp,"7\ny\nlat.pos\ny\n1 1 1 \n1\n3\nC\n0\npos.xyz\ndata.pos\nmap.in\n");
	shell_exec("cd $home;~/latgen <lat.in");
}


function get_latgen(){
	$home=dirname(__FILE__);
	$fp=fopen("$home/POSCAR","r");
	$fout=fopen("$home/lat.pos","w");
	$i=0;
	while($content=fgets($fp)){
		$i++;
		if($i==1 || $i==7)continue;
		fprintf($fout,$content);
	}
		fclose($fp);
	fclose($fout);
}

//read the dump file generated by lammps and return it to vasprun.xml
function get_vasprun(){
	$home=dirname(__FILE__);
	$fp=fopen("$home/dump.force","r");
	$fout=fopen("$home/vasprun.xml","w");
	fprintf($fout,'<varray name="forces" >'."\n");
	for($i=0;$i<9;$i++)fgets($fp);
	while($content=fgets($fp)){
		fprintf($fout,  " <v>  "  .$content." </v>");
		}
			fprintf($fout,'  </varray>'."\n");
	fclose($fp);
	fclose($fout);
	return 	"$home/vasprun.xml";
}
?>
