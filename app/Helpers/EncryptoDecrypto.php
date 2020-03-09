<?php
class Encrypto{
  protected $letterCycle= 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
  protected $spchr = '@#$%';
  protected $randArr = '1234567890qwertyuioplkjhgfdsazxcvbnm1234567890POIUYTREWQASDFGHJKLMNBVCXZ';
  private $key,$loc,$rev;
  function __construct(){
    $this->key= rand(16,25);
    $this->loc= rand(11,30);
    $this->rev= rand(0,1);
  }
  function enc1($x){
    $index= strpos($this->letterCycle,$x);
    return $this->letterCycle[$index+$this->key];
  }
  function enc2($x){
    $k= intval(strval($this->key)[0]);
    $s='';
    for($i=0;$i<$k;$i++){
      for($j=$i;$j<strlen($x);$j+=$k){
        $s.=$x[$j];
      }
    }
    return $s;
  }
  function encrypt(...$vars){
    $temp=array();
    foreach($vars as $i){
      array_push($temp, join('',array_map(array($this,'enc1'),str_split($i))));
    }
    $res= array();
    foreach($temp as $i){
      array_push($res, $this->enc2($i));
    }
    return $res;
  }
  function createMat($data){
    $mat= array_fill(0,100,'*');
    for($i=0;$i<100;$i++){
      $mat[$i]= $this->randArr[rand(0,71)];
    }
    $loc= $this->loc;
    $cp= $loc-1;
    foreach($data as $s){
      $s.=$this->spchr[rand(0,3)].strval(rand(2,8));
      foreach(str_split($s) as $i){
        $mat[$cp++]=$i;
      }
      $cp+=intval($mat[$cp-1])-1;
    }
    $loc= array_map(array($this, 'enc1'),str_split(strval($loc)));
    $k= str_split(strval($this->key));
    $k=chr(ord($loc[1])+intval($k[0])).chr(ord($loc[0]) + intval($k[1]));
    $mat[0]= $this->rev==0?$loc[0]:$loc[1];
    $mat[1]= $this->rev==0?$loc[1]:$loc[0];
    $mat[9]= $this->rev;
    $mat[98]=$k[0];
    $mat[99]=$k[1];
    $m= '';
    $mat= array_chunk($mat,10);
    foreach($mat as $i){
      foreach($i as $j){
        // print_r($j);
        $m.=$j;
      }
      // echo "\n";
    }
    return $m;
  }
  function getCode(...$vars){
    $data=$this->encrypt(...$vars);
    $mat=$this->createMat($data);
    return $mat;
  }
}
class Decrypto extends Encrypto{
  private $key;
  function decp1($x){
    $index= strrpos($this->letterCycle,$x);
    return $this->letterCycle[$index-$this->key];
  }
  function decp2($x){
    $k= intval(strval($this->key)[0]);
    $cop= array_fill(0,strlen($x),'*');
    $c=0;
    $flag=0;
    foreach(str_split($x) as $i){
      $cop[$c]=$i;
      $c+=$k;
      if($c>=strlen($x)){
        $flag+=1;
        $c=0+$flag;
      }
    }
    return join('',$cop);
  }
  function decpCode($mat,$n){
    try{
      $mat= str_split($mat);
      $loc= $mat[9]==0?array($mat[0],$mat[1]):array($mat[1],$mat[0]);
      $k= array($mat[98],$mat[99]);
      $this->key= intval(strval(ord($k[0])-ord($loc[1])).strval(ord($k[1])-ord($loc[0])));
      $loc= intval(join('',array_map(array($this,'decp1'),$loc)));
      $cp= $loc-1;
      $result=array();
      foreach(range(1,$n) as $t){
        $s='';
        while(1){
          if(ctype_alnum($mat[$cp])){
            $s.=$mat[$cp++];
          }
          else{
            break;
          }
        }
        $cp+=intval($mat[$cp+1])+1;
        $s=$this->decp2($s);
        $s= join('',array_map(array($this, 'decp1'),str_split(strval($s))));
        array_push($result,$s);
      }
      return ["status"=>1, "data"=>$result];
    }catch(Exception $e){
      return ["status"=>0, "Exception happened"];
    }
  }
}
?>