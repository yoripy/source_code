<!DOCTYPE html>
<html>
  <head>
	<meta charset="utf-8" />
	<script type="text/javascript">
	history.pushState(null, null, null);
$(window).on("popstate", function (event) {
  if (!event.originalEvent.state) {
    history.pushState(null, null, null);
    return;
  }
});
	</script>
  </head>
  <body>

<?php
	function swap($word){
		$swap_a=$word;
		$swap_b=$word;
		$leng=strlen($word)/2;
		$word = mb_substr($swap_a, -1*$leng, $leng);
		$word .= mb_substr($swap_b, 0, $leng);
		return $word;
	}

	$adrs=8000;
	$adrs2=0x8000;
	$pieces3_count=0;
	$kaigyou_count=0;
	$jump[]=0;
	$func[]=0;
	$hensuu_name[]=0;
	$hensuu_value[]=0;
	$hensuu_pieces[]=0;
	$Hflag=true;
	$check=0;
	$meirei=$_POST['inputTxt'];
	$meirei_copy=$meirei;
	$file=$_POST['inputTxt2'];
	$meirei=trim($meirei);
	$func_i=1;
	$henkan[]=0;
	$henkan_n=0;
	$label[]=0;
	$label2[]=0;
	$label_str[]=0;
	$label_check=0;
	$Syntaxerror=false;
	$Syntaxcheck[]=0;
	$error=true;
	$x0=0x0;
	$label_check_bool=true;
	$org_value=0;
	$hensuu_count=0;
	$hensuu_i=0;
	$hensuu_name[]=0;

	$adrs2=dechex($adrs2);

	if(empty($_POST["inputTxt"])){
		exit('コードが未入力です');
	}
	if(empty($_POST["inputTxt2"])){
		exit('ファイル名が未入力です');
	}

	
		$meirei=mb_convert_kana($meirei, "s");
		$inputfile=$file.".A80";
		$fp=fopen($inputfile, "w");
		fwrite($fp, $meirei);
		fclose($fp);
	

	$meirei=preg_replace('/[\t|\n|\s| 　|,]+/', ' ', $meirei);
	
	$pieces0 = explode(" ", $meirei);
	
	$array_count=count($pieces0);

	$code1=array("CSEG", "HLT", "JZ", "JM", "JNZ", "JS", "JNS", "JC", "JNC", "JP", "JNP", "JPO", "JPE", "NOP", "RIM", "SIM", "JMZ","JMP", "CALL", "CNZ", "CZ", "CNC", "CC", "CPO", "CPE", "CP", "CM", "RET", "RNZ", "RZ", "RNC", "RC", "RPO", "RPE", "RP", "RM", "RLC", "RRC", "RAL", "RAR", "SHLD", "LHLD", "XTHL", "XCHG", "PCHL", "SPHL", "DI", "EI", "DAA", "CMA", "STC", "CMC");
	$code2=array("MOV", "STAX", "LDAX", "ADD", "ADC", "SUB", "SBB", "ANA", "ANA", "XRA", "ORA", "CMP", "DAD", "INX", "DCX", "PUSH", "POP", "OUT", "IN", "RST", "INR", "DCR", );
	$code3=array("MVI", "LXI", "STA", "LDA", "SHLD", "LHLD",  "ADI", "ACI", "SUI", "SBI,", "ANI", "XRI", "ORI", "CPI");
	$hensuu=array("EQU");
	$org=array("ORG");
	$jump_code=array("C3", "C2", "CA", "D2", "DA", "E2", "EA", "F2", "FA");
	
	for ($i=0;$i<$array_count;$i++){
		$pieces0[$i]=trim($pieces0[$i]);
	
	}
	for ($i=0;$i<$array_count;$i++){
		$hensuu_pieces[$i]=$pieces0[$i];
	}

	$s=0;
	for ($i=0;$i<$array_count;$i++){
		if(in_array($hensuu_pieces[$i], $hensuu)){
			$hensuu_name[$s]=$hensuu_pieces[$i-1];
			$hensuu_pieces[$i-1]=NULL;
			$hensuu_pieces[$i]=NULL;
			$i++;
			$hensuu_value[$s]=$hensuu_pieces[$i];
			if(strlen($hensuu_value[$s])==1){
				$hensuu_value[$s]="0".$hensuu_value[$s];
			}
			$moveH_value_a[$s]=mb_substr($hensuu_value[$s], -1, 1);
			
			if(strlen($hensuu_value[$s])==4&&$moveH_value_a[$s]=="H"){
				$hensuu_value[$s]="0".$hensuu_value[$s];
			}
			if(strlen($hensuu_value[$s])==6&&$moveH_value_a[$s]=="H"){
				$moveH_value_b=mb_substr($hensuu_value[$s], 0, 1);
				if($moveH_value_b=="0"){
					$hensuu_value[$s]=mb_substr($hensuu_value[$s], 1);
				}
			}

			if($moveH_value_a[$s]!="H"){
				$hensuu_value[$s]=dechex($hensuu_value[$s]);
				if(strlen($hensuu_value[$s])==1){
					$hensuu_value[$s]="0".$hensuu_value[$s]."H";
				}
				$hensuu_value[$s]=strtoupper($hensuu_value[$s]);
			}
		
			
			$hensuu_pieces[$i]=NULL;
			$s++;
		}
	}
	if($hensuu_name[0]=="0"){
		$hensuu_count=0;
	}else{
		$hensuu_count=count($hensuu_name);
	}
	
	$r=0;
	for ($i=0;$i<$array_count;$i++){
		if(in_array($hensuu_pieces[$i], $org, true)){
			if(strlen($hensuu_pieces[$i+1])==5&&(mb_substr($hensuu_pieces[$i+1], -1, 1)=="H")){
				$hensuu_pieces[$i+1]=rtrim($hensuu_pieces[$i+1], "H");
			}
				$org_value=$hensuu_pieces[$i+1];
				$adrs=$hensuu_pieces[$i+1];
				$adrs2=$hensuu_pieces[$i+1];
				$hensuu_pieces[$i]=NULL;
				$i++;
				$hensuu_pieces[$i]=NULL;
			
		}
	}

	for ($i=0;$i<$array_count;$i++){
		if($hensuu_pieces[$i]=="CSEG"){
			break;
		}
	}
	$newcount=$array_count-$i;
	
	for($j=0;$j<$newcount;$j++){
		$pieces[$j]=$hensuu_pieces[$i];
		$i++;
	}
	
	$array_count=count($pieces);
	if($hensuu_count!="0"){
		for ($i=0;$i<$array_count;$i++){
			if($pieces[$i]==$hensuu_name[$hensuu_i]){
				$pieces[$i]=$hensuu_value[$hensuu_i];
				$hensuu_i++;
			}
			if($i==$array_count-1&&$hensuu_i==$hensuu_count){
				break;
			}
		}
	}

	$s=0;
	$j=0;
	for ($i=0;$i<$array_count;$i++){
		if(in_array($pieces[$i], $code1, true)){
			switch($pieces[$i]){
				case "JZ":
					$label[$s]=$pieces[$i+1];					
					$i++;
					$s++;
					break;
				case "JNZ":
					$label[$s]=$pieces[$i+1];					
					$i++;
					$s++;
					break;
				case "JC":
					$label[$s]=$pieces[$i+1];					
					$i++;
					$s++;
					break;
				case "JNC":
					$label[$s]=$pieces[$i+1];					
					$i++;
					$s++;
					break;
				case "JPO":
					$label[$s]=$pieces[$i+1];				
					$i++;
					$s++;
					break;
				case "JPE":
					$label[$s]=$pieces[$i+1];					
					$i++;
					$s++;
					break;
				case "JMP":
					$label[$s]=$pieces[$i+1];					
					$i++;
					$s++;
					break;
				case "JP":
					$label[$s]=$pieces[$i+1];					
					$i++;
					$s++;
					break;
				case "JM":
					$label[$s]=$pieces[$i+1];					
					$i++;
					$s++;
					break;
				case "CALL":
					$call_stra=substr($pieces[$i+1], 0, 4);
					$call_strb=substr($pieces[$i+1], -1, 1);
					if(preg_match('/[a-fA-F0-9]{4}/', $call_stra)){
						if($call_strb=="H"){
							break;
						}
					}else{
						$label[$s]=$pieces[$i+1];
						$i++;
						$s++;	
					}
					
			}
		}
		else if(in_array($pieces[$i], $code2, true)){
		}
		else if(in_array($pieces[$i], $code3, true)){
		}
		else{
			
		}

	}
	

	$labelcount=count($label);
	$j=0;
	for ($i=0;$i<$array_count;$i++){
		if(in_array($pieces[$i], $code1, true)){
		}
		else if(in_array($pieces[$i], $code2, true)){
		}
		else if(in_array($pieces[$i], $code3, true)){
		}
		else{
				$label_check_a=rtrim($pieces[$i],':');
				
				$label_check_b=mb_substr($pieces[$i], -1);
				if($label_check_b==":"){
					$label2[$j]=mb_substr($pieces[$i], 0, -1);
					$Syntaxerror=true;
				}else if($label_check_b=="H"){
					
					$Syntaxerror=true;
				
				}
				if($pieces[$i]=="SP"){
					$Syntaxerror=true;	
				}
				if($pieces[$i]=="PSW"){
					$Syntaxerror=true;	
				}
				if($Syntaxerror==false){
					if(preg_match('/[a-eA-E]|[H]|[L]|[M]/', $pieces[$i])&&strlen($pieces[$i])==1){	
					}else if(preg_match('/[^a-eA-E]|[^H]|[^L]|[^M]/', $pieces[$i])&&strlen($pieces[$i])==1){
						$Syntaxerror=true;
						print('a~e以外Syntax error');	
					}
					else {
						for($label_i=0;$label_i<$s;$label_i++){
							if($label_check_a==$label[$label_i]){
								$label_check_bool=true;
								break;
							}else{
								$label_check_bool=false;
							}
						}
						if($label_check_bool==false){
							echo"Syntaxの場所::::::$pieces[$i]<br>";
							$Syntaxerror=true;
							$error=false;
						}
					}
				}
				$Syntaxerror=false;	
			}
		}
		if($error==false){
			exit('Syntax error<br>プログラムを終了します');
		}

	$s=0;
	$j=0;
	$r=0;
	for ($i=0;$i<$array_count;$i++){
		if(in_array($pieces[$i], $code1, true)){
			switch($pieces[$i]){
				case "JZ":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "JNZ":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "JC":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "JNC":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "JPO":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "JPE":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "JMP":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "JP":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "JM":
					$jump[$j]=$pieces[$i+1];
					$pieces[$i+1]="0000";
					$j++;
					break;
				case "CALL":
					$call_stra=substr($pieces[$i+1], 0, 4);
					$call_strb=substr($pieces[$i+1], -1, 1);
					if(preg_match('/[a-fA-F0-9]{4}/', $call_stra)){
						if($call_strb=="H"){
							break;
						}
					}
					$func[$s]=$pieces[$i+1];
					$func_number[$s]=$func[$s];
					$pieces[$i+1]="0000";
					$s++;
					break;
			}
		}
		else if(in_array($pieces[$i], $code2, true)){
		}
		else if(in_array($pieces[$i], $code3, true)){
		}
		else if(in_array($pieces[$i], $hensuu_name ,true)){
			if($hensuu_count!=0){
				for($r=0;$r<$hensuu_count;$r++){
					if((string)$pieces[$i]==(string)$hensuu_name[$r]){
						$pieces[$i]=$hensuu_value[$r];
						if((preg_match('/[a-fA-F0-9]{4}/', $pieces[$i]))&&strlen($pieces[$i])==4){
							$pieces[$i]=swap($pieces[$i]);
						}
						break;
					}
				}
			}
		}
		else {
			$moveH[$i]=$pieces[$i];
			$move[$i]=mb_substr($moveH[$i],-1,1);
			if($move[$i]=="H"){
				$Hflag=true;
				if($moveH[$i]=="H"){
					$Hflag=false;
				}
			}
			
			if($move[$i]=="H"&&$Hflag==true) {
				$pieces3_count++;
				$pieces[$i] = mb_substr($pieces[$i],0,-1);
				if(strlen($pieces[$i])==3){
					$pieces[$i] = mb_substr($pieces[$i],0,-1);
					$pieces[$i]=swap($pieces[$i]);
				}
				else if(strlen($pieces[$i])==4){	
					$pieces[$i]=swap($pieces[$i]);
				}
				
			}
			if($move[$i]==":"){
				$pieces[$i]=mb_substr($pieces[$i], 0, -1);
			}
			$Hflag=true;
			
		}
		$pieces3_count++;
	}

	if($jump[0]=="0"){
		$jump_count=0;
	}else{
		$jump_count=count($jump);
	}
	if($func[0]=="0"){
		$func_count=0;
	}else{
		$func_count=count($func);
	}

	$func=array_unique($func);
		$func_count=count($func);

	for ($i=0;$i<$array_count;$i++){
		if(in_array($pieces[$i], $code1, true)){
			$henkan_n++;
			$henkan[$henkan_n]=1;
			switch($pieces[$i]){
				case "CSEG":
					$pieces2[$i]=NULL;
					break;
				case "HLT":
					$pieces2[$i]=0x76;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JZ":
					$pieces2[$i]=0xCA;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JNZ":
					$pieces2[$i]=0xC2;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JC":
					$pieces2[$i]=0xDA;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JNC":
					$pieces2[$i]=0xD2;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JPO":
					$pieces2[$i]=0xE2;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JPE":
					$pieces2[$i]=0xEA;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JMP":
					$pieces2[$i]=0xC3;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JP":
					$pieces2[$i]=0xF2;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "JM":
					$pieces2[$i]=0xFA;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CALL":
					$pieces2[$i]=0xCD;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CNZ":
					$pieces2[$i]=0xC4;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CZ":
					$pieces2[$i]=0xCC;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CNC":
					$pieces2[$i]=0xD4;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CC":
					$pieces2[$i]=0xDC;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CPO":
					$pieces2[$i]=0xE4;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CPE":
					$pieces2[$i]=0xEC;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CP":
					$pieces2[$i]=0xF4;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CM":
					$pieces2[$i]=0xFC;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RET":
					$pieces2[$i]=0xC9;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RNZ":
					$pieces2[$i]=0xC0;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RZ":
					$pieces2[$i]=0xC8;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RNC":
					$pieces2[$i]=0xD0;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RC":
					$pieces2[$i]=0xD8;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RPO":
					$pieces2[$i]=0xE0;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RPE":
					$pieces2[$i]=0xE8;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RP":
					$pieces2[$i]=0xF0;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RM":
					$pieces2[$i]=0xF8;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RLC":
					$pieces2[$i]=0x07;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RRC":
					$pieces2[$i]=0x0F;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RAL":
					$pieces2[$i]=0x17;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RAR":
					$pieces2[$i]=0x1F;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "DI":
					$pieces2[$i]=0xF3;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "EI":
					$pieces2[$i]=0xFB;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "DAA":
					$pieces2[$i]=0x27;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CMA":
					$pieces2[$i]=0x2F;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "STC":
					$pieces2[$i]=0x37;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "CMC":
					$pieces2[$i]=0x3F;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "NOP":
					$pieces2[$i]=0x00;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "RIM":
					$pieces2[$i]=0x20;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "SIM":
					$pieces2[$i]=0x30;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "XTHL":
					$pieces2[$i]=0xE3;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "XCHG":
					$pieces2[$i]=0xEB;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "PCHL":
					$pieces2[$i]=0xE9;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;
				case "SPHL":
					$pieces2[$i]=0xF9;
					$pieces2[$i]=dechex($pieces2[$i]);
					break;

			}
		}
	
		else if(in_array($pieces[$i], $code2, true)){
			$henkan_n++;
			$henkan[$henkan_n]=1;
			switch($pieces[$i]){
				case "MOV":
					switch($pieces[$i+1]){
						case "A":
							switch($pieces[$i+2]){
								case "A":
									$pieces2[$i]=0x7F;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "B":
									$pieces2[$i]=0x78;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "C":
									$pieces2[$i]=0x79;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "D":
									$pieces2[$i]=0x7A;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "E":
									$pieces2[$i]=0x7B;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "H":
									$pieces2[$i]=0x7C;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "L":
									$pieces2[$i]=0x7D;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "M":
									$pieces2[$i]=0x7E;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								default:
									print('MOV命令がSyntax errorの可能性<br>');
									$Syntaxerror=true;
							}
							break;
						case "B":
							switch($pieces[$i+2]){
								case "A":
									$pieces2[$i]=0x47;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "B":
									$pieces2[$i]=0x40;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "C":
									$pieces2[$i]=0x41;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "D":
									$pieces2[$i]=0x42;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "E":
									$pieces2[$i]=0x43;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "H":
									$pieces2[$i]=0x44;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "L":
									$pieces2[$i]=0x45;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "8":
									$pieces2[$i]=0x46;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								default:
								print('MOV命令がSyntax errorの可能性<br>');
								$Syntaxerror=true;
								break;
						}
						break;
						case "C":
							switch($pieces[$i+2]){
								case "A":
									$pieces2[$i]=0x4F;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "B":
									$pieces2[$i]=0x48;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "C":
									$pieces2[$i]=0x49;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "D":
									$pieces2[$i]=0x4A;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "E":
									$pieces2[$i]=0x4B;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "H":
									$pieces2[$i]=0x4C;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "L":
									$pieces2[$i]=0x4D;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "M":
									$pieces2[$i]=0x4E;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
									default:
									print('MOV命令がSyntax errorの可能性<br>');
									$Syntaxerror=true;
							}
						break;
						case "D":
							switch($pieces[$i+2]){
								case "A":
									$pieces2[$i]=0x57;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "B":
									$pieces2[$i]=0x50;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "C":
									$pieces2[$i]=0x51;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "D":
									$pieces2[$i]=0x52;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "E":
									$pieces2[$i]=0x53;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "H":
									$pieces2[$i]=0x54;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "L":
									$pieces2[$i]=0x55;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "M":
									$pieces2[$i]=0x56;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
									default:
									print('MOV命令がSyntax errorの可能性<br>');
									$Syntaxerror=true;
						}
						break;
						case "E":
							switch($pieces[$i+2]){
								case "A":
									$pieces2[$i]=0x5F;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "B":
									$pieces2[$i]=0x58;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "C":
									$pieces2[$i]=0x59;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "D":
									$pieces2[$i]=0x5A;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "E":
									$pieces2[$i]=0x5B;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "H":
									$pieces2[$i]=0x5C;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "L":
									$pieces2[$i]=0x5D;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "M":
									$pieces2[$i]=0x5E;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
									default:
									print('MOV命令がSyntax errorの可能性<br>');
									$Syntaxerror=true;
							}
						break;
						case "H":
							switch($pieces[$i+2]){
								case "A":
									$pieces2[$i]=0x67;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "B":
									$pieces2[$i]=0x60;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "C":
									$pieces2[$i]=0x61;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "D":
									$pieces2[$i]=0x62;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "E":
									$pieces2[$i]=0x63;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "H":
									$pieces2[$i]=0x64;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "L":
									$pieces2[$i]=0x65;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "M":
									$pieces2[$i]=0x66;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
									default:
									print('MOV命令がSyntax errorの可能性<br>');
									$Syntaxerror=true;
							}
						break;
						case "L":
							switch($pieces[$i+2]){
								case "A":
									$pieces2[$i]=0x6F;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "B":
									$pieces2[$i]=0x68;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "C":
									$pieces2[$i]=0x69;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "D":
									$pieces2[$i]=0x6A;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "E":
									$pieces2[$i]=0x6B;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "H":
									$pieces2[$i]=0x6C;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "L":
									$pieces2[$i]=0x6D;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "M":
									$pieces2[$i]=0x6E;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
									default:
									print('MOV命令がSyntax errorの可能性<br>');
									$Syntaxerror=true;
							}
						break;
						case "M":
							switch($pieces[$i+2]){
								case "A":
									$pieces2[$i]=0x77;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "B":
									$pieces2[$i]=0x70;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "C":
									$pieces2[$i]=0x71;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "D":
									$pieces2[$i]=0x72;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "E":
									$pieces2[$i]=0x73;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "H":
									$pieces2[$i]=0x74;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
								case "L":
									$pieces2[$i]=0x75;
									$pieces2[$i]=dechex($pieces2[$i]);
									break;
									default:
									print('MOV命令がSyntax errorの可能性<br>');
									$Syntaxerror=true;
							}
							break;
							default:
							print('Syntax errorの可能性<br>');
							$Syntaxerror=true;
					}
					$pieces[$i+2]=NULL;
				break;
				case "INR":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0x3C;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0x04;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0x0C;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x14;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0x1C;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x24;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0x2C;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0x34;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('INR命令がSyntax errorの可能性<br>');
							$Syntaxerror=true;
					}
				break;
				case "DCR":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0x3D;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0x05;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0x0D;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x15;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0x1D;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x25;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0x2D;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0x35;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('DCR命令がSyntax errorの可能性<br>');
							$Syntaxerror=true;
					}
				break;
				case "MVI":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0x3E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0x06;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0x0E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x16;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0x1E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x26;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0x2E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0x36;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('MVI命令がSyntax errorの可能性<br>');
							$Syntaxerror=true;
					}
				break;
				case "ADD":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0x87;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0x80;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0x81;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x82;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0x83;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x84;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0x85;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0x86;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('ADD命令がSyntax errorの可能性<br>');
							$Syntaxerror=true;
					}
				break;
				case "ADC":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0x8F;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0x88;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0x89;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x8A;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0x8B;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x8C;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0x8D;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0x8E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('ADC命令がSyntax errorの可能性<br>');
							$Syntaxerror=true;
					}
				break;
				case "SUB":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0x97;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0x90;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0x91;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x92;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0x93;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x94;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0x95;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0x96;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('SUB命令がSyntax errorの可能性<br>');
							$Syntaxerror=true;

					}
				break;
				case "SBB":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0x9F;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0x98;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0x99;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x9A;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0x9B;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x9C;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0x9D;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0x9E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('SBB命令がSyntax errorの可能性<br>');
							$Syntaxerror=true;
					}
				break;
				case "ANA":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0xA7;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0xA0;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0xA1;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0xA2;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0xA3;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0xA4;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0xA5;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0xA6;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('ANA命令がSyntax errorの可能性<br>');
							$Syntaxerror=true;
					}
				break;
				case "XRA":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0xAF;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0xA8;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0xA9;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0xAA;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0xAB;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0xAC;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0xAD;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0xAE;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('XRA命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
				break;
				case "ORA":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0xB7;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0xB0;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0xB1;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0xB2;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0xB3;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0xB4;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0xB5;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0xB6;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('ORA命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
				break;
				case "CMP":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0xBF;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0xB8;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0xB9;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0xBA;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0xBB;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0xBC;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0xBD;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0xBE;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('CMP命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
				break;
				case "DAD":
					switch($pieces[$i+1]){
						case "B":
							$pieces2[$i]=0x09;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x19;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x29;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "SP":
							$pieces2[$i]=0x39;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('DAD命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
				break;
				case "INX":
					switch($pieces[$i+1]){
						case "B":
							$pieces2[$i]=0x03;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x13;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x23;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "SP":
							$pieces2[$i]=0x33;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('INX命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
				break;
				case "DCX":
					switch($pieces[$i+1]){
						case "B":
							$pieces2[$i]=0x0B;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x1B;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x2B;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "SP":
							$pieces2[$i]=0x3B;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('DCX命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
				break;
				case "PUSH":
					switch($pieces[$i+1]){
						case "B":
							$pieces2[$i]=0xC5;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0xD5;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0xE5;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "PSW":
							$pieces2[$i]=0xF5;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('PUSH命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
				break;
				case "POP":
					switch($pieces[$i+1]){
						case "B":
							$pieces2[$i]=0xC1;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0xD1;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0xE1;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "PSW":
							$pieces2[$i]=0xF1;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('POP命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
				break;
				case "OUT":
					$pieces2[$i]=0xD3;
					$pieces2[$i]=dechex($pieces2[$i]);
					$pieces2[$i+1]=$pieces[$i+1];
				break;
				case "IN":
					$pieces2[$i]=0xDB;
					$pieces2[$i]=dechex($pieces2[$i]);
					$pieces2[$i+1]=$pieces[$i+1];
				break;
				case "STAX":
					switch($pieces[$i+1]){
						case "B":
							$pieces2[$i+1]=0x02;
							$pieces2[$i+1]=dechex($pieces2[$i+1]);
						break;
						case "D":
							$pieces2[$i+1]=0x12;
							$pieces2[$i+1]=dechex($pieces2[$i+1]);
						break;
						default:
						print('STAX命令がSyntax errorの可能性');
						$Syntaxerror=true;
					}
				break;
				case "LDAX":
					switch($pieces[$i+1]){
						case "B":
							$pieces2[$i+1]=0x0A;
							$pieces2[$i+1]=dechex($pieces2[$i+1]);
						break;
						case "D":
							$pieces2[$i+1]=0x1A;
							$pieces2[$i+1]=dechex($pieces2[$i+1]);
							default:
							print('LDAX命令がSyntax errorの可能性');
							$Syntaxerror=true;
						break;
					}
				break;	
			}
			$pieces[$i+1]=NULL;
		}
		else if(in_array($pieces[$i], $code3, true)){//"MVI", "LXI", "STA", "LDA", "SHLD", "LHLD", "ADI", "ACI", "SUI", "SBI,", "ANI", "XRI", "ORI", "CPI"
			$henkan_n++;
			$henkan[$henkan_n]=1;
			switch($pieces[$i]){
				case "MVI":
					switch($pieces[$i+1]){
						case "A":
							$pieces2[$i]=0x3E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "B":
							$pieces2[$i]=0x06;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "C":
							$pieces2[$i]=0x0E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x16;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "E":
							$pieces2[$i]=0x1E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x26;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "L":
							$pieces2[$i]=0x2E;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "M":
							$pieces2[$i]=0x36;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('MVI命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
					$pieces[$i+1]=NULL;
					$pieces2[$i+2]=$pieces[$i+2];
				break;
				case "LXI":
					switch($pieces[$i+1]){
						case "B":
							$pieces2[$i]=0x01;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "D":
							$pieces2[$i]=0x11;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "H":
							$pieces2[$i]=0x21;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
						case "SP":
							$pieces2[$i]=0x31;
							$pieces2[$i]=dechex($pieces2[$i]);
							break;
							default:
							print('LXI命令がSyntax errorの可能性');
							$Syntaxerror=true;
					}
					$op=$i+2;
					$pieces2[$i+1]=$pieces[$i+2];
					$pieces[$i+1]=NULL;
					$pieces[$i+2]=NULL;
					$i++;
				break;
				case "STA":
					$pieces2[$i] = 0x32;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "LDA":
					$pieces2[$i] = 0x3A;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "SHLD":
					$pieces2[$i] = 0x22;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "LHLD":
					$pieces2[$i] = 0x2A;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "ADI":
					$pieces2[$i] = 0xC6;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "ACI":
					$pieces2[$i] = 0xCE;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "SUI":
					$pieces2[$i] = 0xD6;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "SBI":
					$pieces2[$i] = 0xDE;
					$pieces2[$i]=dechex($pieces2[$i]);
				
				break;
				case "ANI":
					$pieces2[$i] = 0xE6;
					$pieces2[$i]=dechex($pieces2[$i]);
				
				break;
				case "XRI":
					$pieces2[$i] = 0xEE;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "ORI":
					$pieces2[$i] = 0xF6;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
				case "CPI":
					$pieces2[$i] = 0xFE;
					$pieces2[$i]=dechex($pieces2[$i]);
					
				break;
			}
			
		}
		else{
			$henkan_n++;
			$henkan[$henkan_n]=1;
			$pieces2[$i] = $pieces[$i];
		}

	}
	if($Syntaxerror==true){
		exit('Syntax errorです。プログラムを見直して下さい。');
	}
	for ($i=0;$i<$array_count;$i++){
		if(preg_match('/[a-fA-F0-9]/', $pieces2[$i])&&strlen($pieces2[$i])==1){
			$pieces2[$i]=strtoupper($pieces2[$i]);
			$pieces2[$i]=$x0.$pieces2[$i];
		}
	}
	

	$j=0;
	$i=0;
	//16進数2つごとにする整理
	while($i<$array_count){
		if(preg_match('/[a-fA-F0-9]{4}/', $pieces2[$i])){
			$pi_a=$pieces2[$i];
			$pi_b=$pieces2[$i];
			$pieces3[$j]=mb_substr($pi_a, 0, 2);
			$j++;
			$pieces3[$j]=mb_substr($pi_b, -2, 2);
			$j++;
			$i++;
		}else if($pieces2[$i]==NULL){
			$i++;
			if($pieces2[$i]==NULL){
				$i++;
			}
			$pieces3[$j]=$pieces2[$i];
			
		}else{
			$pieces3[$j]=$pieces2[$i];
			$j++;
			$i++;
		}
	}
	
	$array_count2=count($pieces3);

	for($i=0;$i<$array_count2;$i++){
		if(preg_match('/[a-fA-F0-9]{2}/', $pieces3[$i])){
			$pieces3[$i]=strtoupper($pieces3[$i]);
			
		}
	}
	$k=0;
	$s=0;
	$funcflag=false;
	$func_subflag3=false;
	$jumpflag=false;
	$jump_subflag3=false;
	$for_flag=false;
	if($jump[$k]!="0"&&$func[$s]=="0"){
		for($i=0;$i<$array_count2;$i++){
			if($for_flag==true){
				$i=0;
				$for_flag=false;
			}
			if((preg_match('/[a-fA-F0-9]{2}/', $pieces3[$i]))&&strlen($pieces3[$i])==2){
			}else{
				if($pieces3[$i]==$jump[$k]){
					//$jump[$k]==$k;
					$pieces3[$i]=NULL;
					$i++;
					$pieces3[$i]=$pieces3[$i].$jump[$k];
					$k++;
				}
			}

			if($k!=$jump_count&&$i==$array_count2-1){
				$for_flag=true;
				$i=0;
			}else if($k==$jump_count){
				break;
			}
		}
	}else if($func[$s]!="0"&&$jump[$k]=="0"){
		for($i=0;$i<$array_count2;$i++){
			if($for_flag==true){
				$i=0;
				$for_flag=false;
			}
			
			if((preg_match('/([a-f]|[A-F]|[0-9]){2}/', $pieces3[$i]))&&strlen($pieces3[$i])==2){
			}else{
				if($pieces3[$i]==$func[$s]){
					$pieces3[$i]=NULL;
					$i++;
					$pieces3[$i]=$pieces3[$i].$func[$s];
					$s++;
				}
			}

			if($s<=$func_count&&$i==$array_count2-1){
				$for_flag=true;
				$i=0;
			}else if($s==$func_count){
				break;
			}
		}
	}else if($func[$s]!="0"&&$jump[$k]!="0"){
		
		for($i=0;$i<$array_count2;$i++){
			if($for_flag==true){
				$i=0;
				$for_flag=false;
			}

			if((preg_match('/[a-fA-F0-9]{2}/', $pieces3[$i]))&&strlen($pieces3[$i])==2){
			}else{
				if($func_subflag3==false){
					if($pieces3[$i]==$func[$s]){
						$func_subflag3=true;
						$pieces3[$i]=NULL;
						$i++;
					}

					if($func_subflag3==true){
						$pieces3[$i]=$pieces3[$i].$func[$s];
						$func_subflag3=false;
						if($s!=$func_count){
							$s++;
						}
					}

					if($s==$func_count){
						$func_subflag3="end";
					}
				}

				if($jump_subflag3==false){
					if($pieces3[$i]==$jump[$k]){
						$jump_subflag3=true;
						$pieces3[$i]=NULL;
						$i++;
					}
				
					if($jump_subflag3==true){
						$pieces3[$i]=$pieces3[$i].$jump[$k];
						$jump_subflag3=false;
						if($k!=$jump_count){
							$k++;
						}
					}
					if($k==$jump_count){
						$jump_subflag3="end";
					}
				}

				
			}

			if(($s!=$func_count||$k!=$jump_count)&&$i==$array_count2-1){
				$for_flag=true;
				$i=0;
			}else if($s==$func_count&&$k==$jump_count){
				break;
			}
		}
	}

	$j=0;
	$i=0;
	while($i<$array_count2){
		 if($pieces3[$i]==NULL){
			$i++;
			if($pieces3[$i+1]==NULL){
				$i++;
			}
			$pieces4[$j]=$pieces3[$i];
			
		}else{
			$pieces4[$j]=$pieces3[$i];
			$j++;
			$i++;
		}
		
	}

	$jflag=false;
	$fflag=false;
	$j=0;
	$s=0;
	if($org_value=="0"){
		$adrsdec=0x8000;
	}else{
		$adrsdec=hexdec($org_value);
	}
	$array_count3=count($pieces4);
	$for_flag=false;
	
	for($i=0;$i<$array_count3;$i++){
		if($for_flag==true){
			$i=0;
			$for_flag=false;
		}
		$jmp[$j]=substr($pieces4[$i], 2);
		$fnk[$s]=substr($pieces4[$i], 2);
		if($jump_count!=0){
			if($j!=$jump_count){
			}
		}
		if($func_count!=0){
			if($s!=$func_count){
		
			}
		}
		if($jflag==false){
			if($jmp[$j]==$jump[$j])
			{
				$pieces4[$i]=substr($pieces4[$i], 0, 2);
				$jmp_adrs[$j] = $i;
				$hako=strtoupper(dechex($adrsdec+$jmp_adrs[$j]));
				$jmp_adrs[$j]=swap($hako);
				$j++;
				if($j>=count($jump)){
					$jflag=true;
				}
			}
			
		}
		if($fflag==false){
			if($fnk[$s]==$func[$s])
			{
				$pieces4[$i]=substr($pieces4[$i], 0, 2);
				$fnk_adrs[$s] = $i;
				$hako=strtoupper(dechex($adrsdec+$fnk_adrs[$s]));
				$fnk_adrs[$s]=swap($hako);
				$s++;
				if($s>=count($func)){
					$fflag=true;
				}
			}
			
		}
		
		if($j<=$jump_count&&$s<=$func_count&&$i==$array_count3-1){
			$i=0;
			$for_flag=true;
		}else if($j>=$jump_count&&$s>=$func_count){
			break;
		}
		
	}

	$fdrs=0;


	if($jump_count!=0){
		$adrs_count_j=count($jmp_adrs);
		for($i=0;$i<$adrs_count_j;$i++){
		}
	}
	if($func_count!=0){
		$adrs_count_f=count($fnk_adrs);
		for($i=0;$i<$adrs_count_f;$i++){
			$fnk_adrsa[$i]=$fnk_adrs[$i].$func[$i];
			$fdrs++;
		}
	}

	$s=0;
	$j=0;
	$call_count=0;
	for($i=0;$i<$array_count3;$i++){
		if($jflag==true){
			if(in_array($pieces4[$i], $jump_code, true)){
				$adrs_a=mb_substr($jmp_adrs[$j], 0, 2);
				$adrs_b=mb_substr($jmp_adrs[$j], -2, 2);
				$i++;
				$pieces4[$i]=$adrs_a;
				$i++;
				$pieces4[$i]=$adrs_b;
				$j++;
				if($j>=count($jmp_adrs)){
					$jflag=false;
				}
			}
		}
		if($fflag==true){
			if($pieces4[$i]==0xCD){
				$call_count++;
				if($pieces4[$i+1]=="00"){
					if($pieces4[$i+2]=="00"){
						$pieces4[$i]=$pieces4[$i].$func_number[$s];
					}
				}
				$s++;
			}			
		}
	}

	$s=0;
	$func_subflag=false;
	$func_subflag2=false;
	for($i=0;$i<$array_count3;$i++){
		if($func_subflag==true){
			$i==0;
			$func_subflag=false;
		}
		if((preg_match('/[a-fA-F0-9]{2}/', $pieces4[$i]))&&strlen($pieces4[$i])==2){
			$func_subflag2=true;
		}else{
			$func_subflag2=false;
			if(mb_substr($pieces4[$i], 2)==mb_substr($fnk_adrsa[$s], 4)){
				$pieces4[$i]=mb_substr($pieces4[$i], 0, 2);
				$i++;
				$pieces4[$i]=mb_substr($fnk_adrs[$s], 0, 2);
				$i++;
				$pieces4[$i]=mb_substr($fnk_adrs[$s], 2, 2);
			}
		}
		if($i==$array_count3-1&&$func_subflag2==false){
			$func_subflag2=true;
			if($s!=$func_count){
				$s++;
			}
		}
	}

	
	$k=0;
	
	$j=0;
	$l=0;
	$s=0;
	$data_size=0;
	$box;
	$data_memory=0;
	$data_memory2=0;
	$k_flag=0;
	
	for($i=0;$i<$array_count3;$i++){
		if($k!=-16){
			$k_flag=0;
		}
		$box=$i-(16+16*$j);
		$k=$box;
		
		if($k==0){
			$box=$i-(16+16*$j);
			$k=$box;
			$data_size=strtoupper(dechex($i));
			$data_memory=strtoupper(dechex($l));
			if(preg_match('/[a-fA-F0-9]{2}/', $data_size)){
			}else{
				$data_size=$x0.$data_size;
			}
			$hosuu[$j]=hexdec($data_memory)+$hosuu[$j];
			if($j==0){
				$hosuu[$j]=0x80+$hosuu[$j];
			}else{
				$adrs_half=dechex((int)$adrsdec+hexdec((int)$data_memory2));
				$adrs_a=substr($adrs_half, 0, 2);
				$adrs_b=substr($adrs_half, -2, 2);
				$hosuu[$j]=hexdec($adrs_a)+hexdec($adrs_b)+$hosuu[$j];
			}
			$hosuu[$j]=decbin($hosuu[$j]);

			$hosuu_hojo=$hosuu[$j];
			$leng=str_split($hosuu_hojo);
			for($a=0;$a<count($leng);$a++){
				if($leng[$a]==0){
					$leng[$a]=1;
				}else if($leng[$a]==1){
					$leng[$a]=0;
				}
			}
			$hosuuflag=true;
			for($a=count($leng)-1;$a>=0;$a--){
				if($leng[$a]==0){
					$leng[$a]=1;
					break;
				}
				if($leng[$a]==1){
					$leng[$a]=0;
				}
			}
			$length="";
			for($a=0;$a<count($leng);$a++){
				$length=$length.$leng[$a];
			}
			$hosuu[$j]=$length;
			
			$hosuu[$j]=strtoupper(dechex(bindec($hosuu[$j])));
			if(preg_match('/[a-fA-F0-9]{1}/', $hosuu[$j])){
				$hosuu[$j]=$x0.$hosuu[$j];
			}
			if(preg_match('/[a-fA-F0-9]{3,}/', $hosuu[$j])){
				$hosuu[$j]=substr($hosuu[$j], -2, 2);
			}
			$hex[$j]=":".$data_memory.$hex[$j].$hosuu[$j];
			$j++;
			$hex[$j]=dechex(($adrsdec+hexdec((int)$data_size))).$x0.$x0;
			$data_size_copy=$data_size;
			$hex[$j]=$hex[$j].(String)$pieces4[$i];
			$l=1;
			$hosuu[$j]=(int)hexdec($pieces4[$i]);
			$ho=hexdec($pieces4[$i]);

			if($i==$array_count3-1){
				if($k!=-16){
					$k_flag=1;
					$i++;
				}
				break;
			}
			$data_memory2+=$data_memory;
		}else{
			if($i==0){
				$hosuu[$j]=0;
				$hex[$j]=$adrs2.$x0.$x0;
			}
			$l++;
			$hex[$j]=$hex[$j].(String)$pieces4[$i];
			$hosuu[$j]=$hosuu[$j]+(int)hexdec($pieces4[$i]);
			$ho=hexdec($pieces4[$i]);
			$s++;
		}
		$box=$i-(16+16*$j);
		$k=$box;
		if($k!=-16){
			$k_flag=1;
		}
	}
	
	if($k_flag==1&&$i==$array_count3){
		$data_size=strtoupper(dechex($l));
			if(preg_match('/[a-fA-F0-9]{2}/', $data_size)){
			}else{
				$data_size=$x0.$data_size;
			}
			$hosuu[$j]=hexdec($data_size)+$hosuu[$j];
			if($j==0){
				$hosuu[$j]=0x80+$hosuu[$j];
			}else{
				$adrs_half=dechex($adrsdec+hexdec((int)$data_size_copy));
				$adrs_a=substr($adrs_half, 0, 2);
				$adrs_b=substr($adrs_half, -2, 2);
				$hosuu[$j]=hexdec($adrs_a)+hexdec($adrs_b)+$hosuu[$j];
			}
			$hosuu[$j]=decbin($hosuu[$j]);
			$hosuu_hojo=$hosuu[$j];
			$leng=str_split($hosuu_hojo);
			for($a=0;$a<count($leng);$a++){
				if($leng[$a]==0){
					$leng[$a]=1;
				}else if($leng[$a]==1){
					$leng[$a]=0;
				}
			}

			for($a=count($leng)-1;$a>=0;$a--){
				if($leng[$a]==0){
					$leng[$a]=1;
					break;
				} 
				if($leng[$a]==1){
					$leng[$a]=0;
				}
			}
			$length="";
			for($a=0;$a<count($leng);$a++){
				$length=$length.$leng[$a];
			}
			$hosuu[$j]=$length;
			$hosuu[$j]=strtoupper(dechex(bindec($hosuu[$j])));
			if(preg_match('/[a-fA-F0-9]{3,}/', $hosuu[$j])){
				$hosuu[$j]=substr($hosuu[$j], -2, 2);
			}
		$hex[$j]=":".$data_size.$hex[$j].$hosuu[$j];
	}
	
	
	$j++;
	$x1=0x1;
	$x1=dechex($x1);
	$x2=0xff;
	$x2=dechex($x2);
	$hex[$j]=":".$x0.$x0.$x0.$x0.$x0.$x0.$x0.$x1.$x2;
	$hex[$j]=strtoupper($hex[$j]);

	$hex_count=count($hex);
	$filename=$file.'.hex';
	$fp=fopen($filename, 'w');
	echo"アセンブルが完了しました<br>";
	for($i=0;$i<$hex_count;$i++){
		echo "$hex[$i]<br>";
		if($i==$hex_count-1){
			fwrite($fp, $hex[$i]);
		}else{
			fwrite($fp, $hex[$i]."\n");
		}
	}
	
	fclose($fp);
	
?>
<h1>ダウンロード</h1>
ダウンロードしたいファイルの方を右クリックで「リンク先の保存」をしてください<br>
	<a href='<?php echo $filename;?>' >HEXファイルダウンロード</a><br>
	<a href='<?php echo $inputfile;?>' >入力ファイルダウンロード</a>
<br><h1>入力ページに戻る場合は下のボタンをクリック</h1>
	<form method="post" action="php_test - 2_set - 4.php">
		<input type="hidden" name="check" value="true">
		<input type="hidden" name="file1" value="<?php echo $inputfile;?>">
		<input type="hidden" name="file2" value="<?php echo $filename;?>">
		<input type="submit" value="入力ページへ戻る">
	</form>
 </body>
</html>