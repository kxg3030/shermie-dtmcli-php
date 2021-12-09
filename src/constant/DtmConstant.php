<?php

namespace sett\constant;

class DtmConstant
{
    // 获取新的事务ID
    const GetNewGidPath = "/api/dtmsvr/newGid";
    // 事务准备
    const TransPreparePath = "/api/dtmsvr/prepare";
    // 事务提交
    const TransSubmitPath = "/api/dtmsvr/submit";
    // 事务回滚
    const TransAbortPath = "/api/dtmsvr/abort";
    // 注册事务分支
    const RegisterBranchPath = "/api/dtmsvr/registerBranch";
    // 注册xa事务分支
    const RegisterXaBranchPath = "/api/dtmsvr/registerXaBranch";
    // 注册tcc事务分支
    const RegisterTccBranchPath = "/api/dtmsvr/registerTccBranch";
    // 查询单个gid事务
    const QueryGidTransPath = "/api/dtmsvr/query";
    // 查询所有事务
    const QueryAllTransPath = "/api/dtmsvr/all";

    // saga事务
    const SagaTrans = "saga";
    // tcc事务
    const TccTrans = "tcc";
    // msg事务
    const MsgTrans = "msg";
    // xa事务
    const XaTrans = "xa";
    // 返回状态
    const Success = "SUCCESS";
    const Failure = "FAILURE";
    // 事务状态
    const ActionTry        = "try";
    const ActionConfirm    = "confirm";
    const Action           = "action";
    const ActionCompensate = "compensate";
    const ActionComment    = "comment";
    const ActionRollback   = "rollback";
    const ActionCancel   = "cancel";
}
