<?php
session_start();
$mdp = "Bamako123";
$mdphash = sha1($mdp);
echo $mdphash;
	require 'frontend/Form.class.php';
	require 'backend/Login.class.php';
	$value = '';
	if(isset($_SESSION['id'])){ header('Location: contenu/accueil.php'); }
	if(isset($_POST['run'])) {
		if(!empty($_POST['identifiant']) AND !empty($_POST['mot_de_passe'])) {
			$identifiant = htmlspecialchars($_POST['identifiant']);
			$mot_de_passe = $_POST['mot_de_passe'];
			$mot_de_passe_hache = sha1($mot_de_passe);
			if(isset($_POST['se_souvenir_de_moi']) AND $_POST['se_souvenir_de_moi'] == 'on') {
				setcookie('identifiant', $identifiant, time() + 365*24*3600, null, null, false, true);
				setcookie('mot_de_passe', $mot_de_passe, time() + 365*24*3600, null, null, false, true);
			}
			try{
				$db = new Database();
				$db->get_pdo();
				$req = $db->get_pdo()->prepare('SELECT * FROM Utilisateur WHERE (nom_utilisateur = :identifiant OR email = :identifiant ) AND mot_de_passe = :mot_de_passe');
				$req->execute(array('identifiant' => $identifiant, 'mot_de_passe' => $mot_de_passe_hache));
				$resultat = $req->rowCount();
				if($resultat == 1) {
					$userinfo = $req->fetch();
					$_SESSION['id'] = $userinfo['id_utilisateur'];
					$_SESSION['mail'] = $userinfo['email'];
					$_SESSION['id_category'] = $userinfo['id_categorie'];
					$_SESSION['username'] = $userinfo['nom_utilisateur'];
					$_SESSION['password'] = $userinfo['mot_de_passe'];
					header('Location: contenu/accueil.php?page=1');
				} else {
					$erreur = 'Mauvais identifiant';
				}
			} catch(Exception $e){
				die('Erreur'. $e->getMessage());
			}
		} else {
			$erreur = 'Veuillez verifier que tous les champs sont remplis !';
		}
	}
	$form = new Form();
?>
<!DOCTYPE html>
<html>
	<head>
		<base href="/aptech-app/">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="style/css/main.css">
		<link rel="stylesheet" type="text/css" href="style/font/css/all.css">
		<link rel="stylesheet" type="text/css" href="style/css/style.css">
		<title></title>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<!-- <img src="media/img/background_02.gif" class="position-absolute h-100 w-100"> -->
				<div class="box offset-lg-3 offset-md-2 col-md-8 col-lg-6 col-sm-12 offset-md-2 offset-lg-3 mt-lg-10 bg-light shadow-lg p-4">
					<div class="mw-100 h1 display-3 text-center">
						<span class="fa fa-expand text-primary"></span>
					</div>
					<div class="h4 font-weight-bold text-center mb-4">
						Se connecter à son espace universitaire !
					</div>
					<div class="col-12 pt-4">
						<form class="form" method="POST">
							<div class="form-group">
								<?php $form->label('mail', 'Nom d\'utilisateur ou Adresse Mail', '"font-weight-bold h6"'); ?>
								<!-- <label for="mail" class="font-weight-bold h6">Nom d'utilisateur ou Adresse Mail</label> -->
								<!-- <input type="text" id="mail" name="" class="form-control" placeholder="Nom d'utilisateur ou Adresse Mail"> -->
								<?php $form->input('text', 'identifiant', 'mail', 'form-control', '"Nom d\'utilisateur"', $value); ?>
							</div>
							<div class="form-group">
								<?php $form->label('mdp', 'Mot de passe', 'font-weight-bold h6'); ?>
								<!-- <label for="mdp"  class="font-weight-bold h6">Mot de passe</label> -->
								<!-- <input type="password" id="mdp" name="" class="form-control" placeholder="Mot de passe"> -->
								<?php $form->input('password', 'mot_de_passe', 'mdp', 'form-control', '"Mot de passe"'); ?>
							</div>
							<div class="form-group">
								<?php $form->label('en_ligne', 'Se souvenir de moi sur cet appareil', 'small'); ?>
								<!-- <label for="en_ligne">Se souvenir de moi sur cet appareil</label> -->
								<?php $form->input('checkbox', 'se_souvenir_de_moi', 'en_ligne', 'ml-2') ?>
								<!-- <input type="checkbox" id="en_ligne" name="" class="ml-2"> -->
							</div>
							<div class="form-group">
								<?php $form->btn('submit', 'run', 'Se connecter', '"btn btn-primary w-100"'); ?>
								<!-- <button class="btn btn-primary w-100">Se connecter</button> -->
							</div>
							<?php
								if(isset($erreur)){ ?>
									<div class="text-center alert alert-danger small">
										<?= $erreur; ?>
									</div>
							<?php
								}
							?>
							<div class="text-center">
								<a href="#">Mot de passe oublié ?</a>
							</div>
						</form>
						<div class="row mt-4">
							<div class="col-12 small d-inline">
								<a href="#" class="text-dark p-2">Contact</a>
								<a href="#" class="text-dark p-2">À propos</a>
								<a href="#" class="text-dark p-2">Aide</a>
								<a href="#" class="text-dark p-2">Politique de confidentialité</a>
								<a href="#" class="text-dark p-2">Règles de gestion</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="style/js/jquery.js"></script>
		<script src="style/js/app.js"></script>
	</body>
</html>