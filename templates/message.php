<?php
session_start();
if(!empty($_SESSION['id']) AND $_SESSION['id'] > 0) {
    require_once 'include/header.php';
    require_once 'include/aside.php';
    require_once '../models/Message.php';
    $message = new \Model\Message(); ?>
    <div class="col-12 col-lg-9 col-xl-9">
        <h3 class="text-center font-weight-bold">Boîte de reception</h3>
        <div class="col-12">
            <table class="table">
                <tbody>
                <tr>
                    <?php
                    if(!empty($message->getReception($_SESSION['id']))) {
                        foreach ($message->getReception($_SESSION['id']) as $donnees): ?>
                            <td class="row">
                                <?php
                                if($_SESSION['id'] != $donnees['sender_id']) { ?>
                                    <span class="font-weight-bold"><a href="ecrire_message.php?id=<?= $donnees['sender_id'] ?>">@<?= $donnees['username'] ?></a> </span>
                                    <span class="opacity-4 pl-2"><?= $donnees['content'] ?></span>
                                    <span class="pl-3 opacity-1 small"><?= $donnees['send_date'] ?></span>
                                 <?php
                                }
                                ?>

                            </td>
                         <?php
                            endforeach;
                    } else { ?>
                       <td class="row">Votre boîte de reception est vide ...</td>
                   <?php
                    }
                    ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php
} else {
    header("Location: ../index.php");
}