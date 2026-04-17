<?php

namespace Cable8mm\MmaScrapers\Enums;

/**
 * Enum representing the method by which a fight was decided.
 */
enum FightMethod: string
{
    case KO = 'ko';
    case TKO = 'tko';
    case SUBMISSION = 'submission';
    case DECISION = 'decision';
    case DQ = 'disqualification';
    case NC = 'no contest';
}
