<?php
	
	class UserController extends AbstractController{

		protected $gdb=null;

		function __construct(){
				$this->gdb=userSPDO::singleton();
		}

		function index(){
			if ($_SERVER['REQUEST_METHOD']!='GET'){
				return array('error'=>'Petició no vàlida');
			}
			$sql="SELECT id,nom,email FROM usuari";
			$query=$this->gdb->prepare($sql);
			$query->execute();
			$rows=$query->fetchAll(PDO::FETCH_OBJ);
			return $rows;

		}

		function login($request){
			//recuperar dades de $_POST
			if ($_SERVER['REQUEST_METHOD']!='POST'){
				return array('error'=>'Petició no vàlida');
			}else{
				
				$email=$request->parameters['email'];
				$pwd=$request->parameters['pwd'];
				//realitzar consulta SQL
				$sql="SELECT email,pwd FROM usuari WHERE email=? AND pwd=?";
				$query=$this->gdb->prepare($sql);
				$query->execute(array($email,$pwd));
				$res=$query->fetch();
				
				if($query->rowCount()==1){
					return array('msg'=>'Login correct');

				}
				else {
					return array('msg'=>'User  not exists');
				}
			}
		}

		function creaUsuari($request){
			//recuperar dades de $_POST
			if($_SERVER['REQUEST_METHOD']!='POST'){
				return array('error'=>'Request not valid');
			}
			
			$sql="INSERT INTO usuari(id,nom,email,pwd) VALUES (?,?,?,?)";
			$query=$this->gdb->prepare($sql);
			$query->bindParam(1,$request->parameters['id']);
			$query->bindParam(2,$request->parameters['nom']);
			$query->bindParam(3,$request->parameters['email']);
			$query->bindParam(4,$request->parameters['pwd']);
			$res=$query->execute();
			if($res==1){
				return array('msg'=>'user inserted');
			}
			else{
				return array('msg'=>'error inserting user');
			}

		}
		
		
}
