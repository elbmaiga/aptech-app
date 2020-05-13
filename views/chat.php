<div class="col-12 col-lg-8">
    <h1 class="title mt-n3 font-weight-bold">Salon de chat</h1>
    <div class="col-12 bg-light">
        <div class="row">
            <div class="col-12">
                <form method="POST">
                    <?php
                    $chat = new \Model\Chat();
                    $form = new \App\Form();
                    // Form submitted
                    if(isset($_POST['submitted'])) {
                        if(!empty($_POST['message'])) {
                            if(strlen($_POST['message']) <= 255) {
                                $chat->insert($_SESSION['id'], $_POST['message']);
                                $success = "Message envoyé avec succès";
                            }else {
                                $erreur = "Votre message ne doit pas dépasser 255 caractères !";
                            }
                        } else {
                            $erreur = "Vous ne pouvez pas envoyé en message vide !";
                        }
                    }
                    ?>
                    <div class="input-field">
                        <?php
                        $form->textarea("message", "message", "materialize-textarea", "255");
                        $form->label("message", "<span class='fa fa-envelope'></span> Écrivez votre message ici ...");
                        ?>
                    </div>
                    <div class="input-field">
                        <?php $form->btn("submit", "submitted", "Envoyer", '"btn left"'); ?>
                    </div>
                </form>
            </div>
            <div class="col-12 p-4 mb-4">
                <div id="chat">
                    <?php
                    if(!empty($chat->findAll())) {
                        foreach ($chat->findAll("INNER JOIN users ON users.id = chat.users_id ORDER BY send_date DESC LIMIT 0, 15") as $datas): ?>
                            <ol class="list-unstyled messages overflow-auto">
                                <?php
                                if($_SESSION['id'] == $datas['id']) { ?>
                                    <li class="me">
                                        <div class="message teal white-text col-6">
                                            <?= $datas['content'] ?>
                                        </div>
                                        <time><?= $datas['send_date'] ?></time>
                                    </li>
                                    <!--<div class="chip">
                    <img src="public/media/img/profile.jpg" alt="Contact Person">
                    <span class="font-weight-bold">Moi:</span>
                    <span class="ml-2"><?= nl2br($datas['content']); ?></span>
                    <small><?= $datas['send_date']; ?></small>
                </div> -->
                                    <?php
                                } else { ?>
                                    <li class="dest">
                                        <div class="message_dest black white-text col-6">
                                            <div class="chip black white-text">
                                                <img src="public/media/img/profile.jpg" alt="Contact Person">
                                                <span class="font-weight-bold"><a href="index.php?page=read_message&&id=<?= $datas['users_id'] ?>"><?= $datas['username'] ?></a>:</span>
                                            </div>
                                            <?= $datas['content'] ?>
                                        </div>
                                        <time class="d-block"><?= $datas['send_date'] ?></time>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ol>
                        <?php
                        endforeach;
                    } else { ?>
                        <div class="row">Pas de message dans le salon de chat ...</div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-none d-lg-block col-lg-4 mt-4">
    <?php
    $user = new \Model\User();
    $user->online($_SESSION['id']);
    ?>
    <ul class="collection with-header">
        <li class="collection-header teal white-text font-weight-bold"><span><?= count($user->online($_SESSION['id'])) ?> Utilisateur(s) en ligne.</span></li>
        <?php
        foreach($user->online($_SESSION['id']) as $user_online):
        ?>
        <li class="collection-item font-weight-bold"><span class="fa fa-user text-success"></span> <?= $user_online['username'] ?></li>
        <?php
        endforeach;
        ?>
    </ul>
</div>