<?php

namespace App\Controller;

use Framework\View\Model\JsonModel;
use Framework\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    public function init() {
        parent::init();
    }

    public function memberListAction()
    {
    	$where = array();
    	if ($this->param('member_name')) {
    		$where[] = sprintf("member_name LIKE '%s'", addslashes('%' . $this->helpers->escape(trim($this->param('member_name'))) . '%'));
    	}
    	if ($this->param('member_status') === '0' || $this->param('member_status') === '1') {
    		$where[] = sprintf("member_status = %d", $this->param('member_status'));
    	}

    	$count = $this->models->member->getCount(array('setWhere' => $where));
    	$this->helpers->paginator($count, 10);
    	$limit = array($this->helpers->paginator->getLimitStart(), $this->helpers->paginator->getItemCountPerPage());

    	$files = array('*');
    	$sqlInfo = array(
    		'setWhere' => $where,
    		'setLimit' => $limit,
    		'setOrderBy' => 'member_id DESC',
    	);

    	$memberList = $this->models->member->getMember($files, $sqlInfo);

    	// print_r($memberList);die;
        return array(
        	'memberList' => $memberList,
        );
    }

    public function memberEditAction()
    {
    	$memberId = $this->param('member_id');
    	$memberInfo = array();
    	if ($memberId) {
    		$memberInfo = $this->models->member->getMemberById($memberId);
    	}

    	if ($this->funcs->isAjax()) {
    		if (!$memberId) {
    			$memberName = trim($_POST['member_name']);
                if (!$memberName) {
                    return new JsonModel('error', '用户名不能为空');
                }

    			$member = $this->models->member->getMemberByName($memberName);
    			if ($member) {
    				return new JsonModel('error', '用户名已存在');
    			}

    			$password = trim($_POST['member_password']);
    			if (!$password) {
    				return new JsonModel('error', '密码不能为空');
    			}

    			$map = array(
    				'member_name' => trim($_POST['member_name']),
    				'member_password' => $this->password->encrypt($password),
    				'member_status' => $_POST['member_status'],

    			);
    			$sql = "INSERT INTO t_member 
    					SET member_name = :member_name,
    					member_password = :member_password,
    					member_status = :member_status";
    		} else {
    			$map = array(
    				'member_status' => $_POST['member_status'],
    				'member_id' => $memberId,
    			);
    			$sql = "UPDATE t_member 
    					SET member_status = :member_status
    					WHERE member_id = :member_id";
    		}

            $status = $this->locator->db->exec($sql, $map);
    		if ($status) {
    			return JsonModel::init('ok', '成功')->setRedirect($this->helpers->url('admin/member-list'));
    		} else {
    			return new JsonModel('error', '失败');
    		}
    	}
    	return array(
    		'memberInfo' => $memberInfo,
    	);
    }

    public function memberDelAction()
    {
    	if (!$this->funcs->isAjax()) {
    		$this->funcs->redirect($this->helpers->url('default/index'));
    	}

    	$memberId = $this->param('member_id');
    	if ($memberId) {
    		$memberInfo = $this->models->member->getMemberById($memberId);
    		if ($memberInfo['member_name'] == 'admin') {
    			return new JsonModel('error', '超级管理员禁止删除');
    		}
    	}
    	
    	$sql = "DELETE FROM t_member WHERE member_id = ?";
    	$status = $this->locator->db->exec($sql, $memberId);
    	if ($status) {
    		return JsonModel::init('ok', '成功');
    	} else {
    		return new JsonModel('error', '失败');
    	}
    }
}
