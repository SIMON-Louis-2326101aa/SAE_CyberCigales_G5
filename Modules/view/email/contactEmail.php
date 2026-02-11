<?php
$date = date('d/m/Y à H:i:s');
?>

<div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
    <table width="100%"><tr><td align="center">
                <table width="600" style="background:#ffffff; padding:20px; border-radius:8px;">
                    <tr><td>
                            <h2>Nouveau Message Contact</h2>

                            <p><strong>De :</strong> <?= htmlspecialchars($email) ?></p>
                            <p><strong>Sujet :</strong> <?= htmlspecialchars($sujet) ?></p>

                            <div>
                                <?= nl2br(htmlspecialchars($message)) ?>
                            </div>

                            <p>Envoyé le <?= $date ?></p>
                        </td></tr>
                </table>
            </td></tr></table>
</div>
