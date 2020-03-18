<?php

/*
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/
$config['registration'] = array(
    array(
        'field' => 'name',
        'label' => 'Name',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'phone',
        'label' => 'Phone Number',
        'rules' => 'trim|required|numeric|is_unique[sb_user_infos.phone]',
        'errors' => array(
            'is_unique' => 'This Phone Number is already in use'
        )
    ),
    array(
        'field' => 'email',
        'label' => 'Email',
        'rules' => 'trim|is_unique[sb_users.user_email]',
        'errors' => array(
            'is_unique' => 'This Email is already in use'
        )
    ),
    array(
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'trim|required|min_length[6]'
    ),
    array(
        'field' => 'confirm_password',
        'label' => 'Confirm Password',
        'rules' => 'trim|required|matches[password]'
    ),
    array(
        'field' => 'school',
        'label' => 'School',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'district',
        'label' => 'District',
        'rules' => 'trim|required',
    ),
    array(
        'field' => 'class',
        'label' => 'Class',
        'rules' => 'required',
    ),
    array(
        'field' => 'dob',
        'label' => 'Date Of Birth',
        'rules' => 'required',
    )
);
$config['game'] = array(
    array(
        'field' => 'token',
        'label' => 'Token',
        'rules' => 'trim|required'
    ),
);

$config['update_profile'] = array(
    array(
        'field' => 'class',
        'label' => 'Class',
        'rules' => 'required',
    ),
    array(
        'field' => 'dob',
        'label' => 'Date Of Birth',
        'rules' => 'required',
    )
);

$config['password_reset_request'] = array(
     array(
        'field' => 'email',
        'label' => 'Email',
        'rules' => 'trim|required|is_unique[sb_users.user_email]',
        'errors' => array(
            'is_unique' => 'This Email is already in use'
        )
    ),
    array(
        'field' => 'class',
        'label' => 'Class',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'dob',
        'label' => 'Date of Birth',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'phone',
        'label' => 'Phone',
        'rules' => 'trim|required'
    )
);

$config['change_password'] = array(
    array(
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'trim|required|min_length[6]'
    ),
    array(
        'field' => 'confirm_password',
        'label' => 'Confirm Password',
        'rules' => 'trim|required|matches[password]'
    ),
);

$config['word_check'] = array(
    array(
        'field' => 'token',
        'label' => 'Token',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'word_id',
        'label' => 'Word ID',
        'rules' => 'trim|required'
    )
);

$config['division_leaders'] = array(
    array(
        'field' => 'division_id',
        'label' => 'Division ID',
        'rules' => 'trim|required'
    )
);
$config['practice'] = array(
    array(
        'field' => 'word_id',
        'label' => 'Word ID',
        'rules' => 'trim|required'
    )
);
$config['game_complete'] = array(
    array(
        'field' => 'token',
        'label' => 'Token',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'completed',
        'label' => 'Completed',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'score',
        'label' => 'Score',
        'rules' => 'trim|required'
    )
);
