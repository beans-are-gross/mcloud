<?php
echo password_hash($_GET["pwd"], PASSWORD_BCRYPT);