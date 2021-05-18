<?php

namespace RexShijaku\SQLToCIBuilder\utils;

class SelectQueryTypes
{
    const Aggregate = 'aggregate';
    const CountATable = 'count_a_table';
    const Get = 'get';
    const GetWhere = 'get_where';
    const Other = 'other';

    const CLOSURES = array(SelectQueryTypes::CountATable,
        SelectQueryTypes::Get,
        SelectQueryTypes::GetWhere);
}