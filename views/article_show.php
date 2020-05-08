<div class="col-12 mt-n4">
    <?php
    if(isset($_GET['id']) AND $_GET['id'] > 0) {
        $getid = intval($_GET['id']);
        $article = new \Model\Article();
        if(!empty($article->find($getid))) {
            foreach ($article->find($getid) as $datas):
                $page_title = $datas['subject'];
                echo "
                    <article>
                        <h1 class='title teal white-text p-3'>$datas[subject]</h1>
                        <p class='col-12 justify'>$datas[content]</p>
                        <p class='col-2 opacity-1'>$datas[send_date]</p>
                    </article>
                ";
            // Edit or delete article if admin
            if($_SESSION['id'] === 1): ?>
                <div class="row">
                <div class="col-4 col-lg-3">
                    <a class="waves-effect waves-light btn modal-trigger" href="#edit"><span class="fa fa-pen"></span> Modifier l'article</a>
                </div>
                <div class="col-4 col-lg-3">
                    <a class="waves-effect waves-light red darken-2 btn modal-trigger" href="#delete"><span class="fa fa-trash"></span> Supprimer l'article</a>
                </div>
            </div>
            <div id="edit" class="modal">
                <div class="modal-content">
                    <h4>Modifier l'article</h4>
                    <?php
                    if(isset($_POST['edited'])) {
                        if(!empty($_POST['subject']) AND !empty($_POST['content'])) {
                            $article->update($_GET['id'], $_POST['subject'], $_POST['content']);
                            redirectTo("index.php?page=home&&action=show&&id=$getid");
                        } else {
                            $erreur = 'Les champs ne peuvent restés vides !';
                            header("Location: accueil.php");
                        }
                    }
                    ?>
                    <form method="POST">
                        <div class="input-field">
                            <input name="subject" type="text" class="validate" id="title" value="<?= $datas['subject'] ?>">
                            <label for="subject">Titre de l'article</label>
                        </div>
                        <div class="input-field">
                            <textarea name="content" class="materialize-textarea" id="content"><?= $datas['content'] ?></textarea>
                            <label for="content">Contenu de l'article</label>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn indigo lighten-5 black-text" data-dismiss="modal">Annuler</button>
                            <button type="submit" name="edited" class="btn btn-success">Engregister</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            if(isset($_POST['delete'])) {
                $article->delete($getid);
               redirectTo("index.php?page=home");
            }
            ?>

            <div id="delete" class="modal">
                <div class="modal-content">
                    <h4>Demande de confirmation</h4>
                    <p>Êtes-vous sûr de vouloir supprimer cet article ?</p>
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <button type="submit" class="btn ">Annuler</button>
                        <button type="submit" name="delete" class="btn red darken-2">Supprimer</button>
                    </form>
                </div>
            </div>

            <?php
            endif;
            endforeach;
            ?>
            <ul class="collection with-header">
                <li class="collection-header font-weight-bold h4">Commentaires ...</li>
                <?php
                $comment = new \Model\Comment();

                //Insert comments
                if(isset($_POST['submitted'])) {
                    if(!empty($_POST['content'])) {
                        $comment->insert(1, $_SESSION['id'], $getid, $_POST['content']);
                        $success = "Votre commentaire a bien été publié !";
                    } else {
                        $erreur = "Veuillez rédiger un commentaire SVP !";
                    }
                }

                // Show comments
                if(!empty($comment->findBy(1, $getid))) {
                    foreach ($comment->findBy(1, $getid) as $donnees): ?>
                        <li class="collection-item">
                            <span class="font-weight-bold">@<?= $donnees['username'] ?></span>
                            <span class="opacity-4 pl-2"><?= $donnees['content'] ?></span>
                            <span class="pl-3 d-block opacity-1 small"><?= $donnees['pub_date'] ?></span>
                        </li>
                    <?php
                    endforeach;
                } else { ?>
                    <li class="collection-item">Pas de commentaire sur cet article !</li>
                    <?php
                }
                ?>
            </ul>
            <form method="POST" class="form">
                <div class="container">
                    <div class="input-field">
                        <?php
                        $form->textarea("content", "content");
                        $form->label("content", "Rédiger un commentaire ...");
                        $form->btn("submit", "submitted", "Publier", "'btn right'");
                        ?>
                    </div>
                    <?php //$form->get_error(isset($erreur) ? $erreur : NULL) ?>
                    <?php //$form->get_success(isset($success) ? $success : NULL) ?>
                </div>
            </form>
            <?php
        } else {
            redirectTo("index.php?page=error");
        }
    } else {
        redirectTo("index.php?page=home");
    }
    ?>
</div>