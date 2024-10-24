<?php declare(strict_types=1);
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2007 ILIAS open source, University of Cologne            |
    |                                                                             |
    | This program is free software; you can redistribute it and/or               |
    | modify it under the terms of the GNU General Public License                 |
    | as published by the Free Software Foundation; either version 2              |
    | of the License, or (at your option) any later version.                      |
    |                                                                             |
    | This program is distributed in the hope that it will be useful,             |
    | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
    | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
    | GNU General Public License for more details.                                |
    |                                                                             |
    | You should have received a copy of the GNU General Public License           |
    | along with this program; if not, write to the Free Software                 |
    | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
    +-----------------------------------------------------------------------------+
*/
/*
    PHP port of ADL SeqConditionSet.java
    @author Hendrik Holtmann <holtmann@mac.com>

    This .php file is GPL licensed (see above) but based on
    SeqConditionSet.java by ADL Co-Lab, which is licensed as:

    Advanced Distributed Learning Co-Laboratory (ADL Co-Lab) Hub grants you
    ("Licensee") a non-exclusive, royalty free, license to use, modify and
    redistribute this software in source and binary code form, provided that
    i) this copyright notice and license appear on all copies of the software;
    and ii) Licensee does not utilize the software in a manner which is
    disparaging to ADL Co-Lab Hub.

    This software is provided "AS IS," without a warranty of any kind.  ALL
    EXPRESS OR IMPLIED CONDITIONS, REPRESENTATIONS AND WARRANTIES, INCLUDING
    ANY IMPLIED WARRANTY OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
    OR NON-INFRINGEMENT, ARE HEREBY EXCLUDED.  ADL Co-Lab Hub AND ITS LICENSORS
    SHALL NOT BE LIABLE FOR ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF
    USING, MODIFYING OR DISTRIBUTING THE SOFTWARE OR ITS DERIVATIVES.  IN NO
    EVENT WILL ADL Co-Lab Hub OR ITS LICENSORS BE LIABLE FOR ANY LOST REVENUE,
    PROFIT OR DATA, OR FOR DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL,
    INCIDENTAL OR PUNITIVE DAMAGES, HOWEVER CAUSED AND REGARDLESS OF THE
    THEORY OF LIABILITY, ARISING OUT OF THE USE OF OR INABILITY TO USE
    SOFTWARE, EVEN IF ADL Co-Lab Hub HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH
    DAMAGES.
*/

    define("EVALUATE_UNKNOWN", 0);
    define("EVALUATE_TRUE", 1);
    define("EVALUATE_FALSE", -1);
    define("COMBINATION_ALL", "all");
    define("COMBINATION_ANY", "any");
    
    /******************************************************************************
     *
     * This file is part of ILIAS, a powerful learning management system.
     *
     * ILIAS is licensed with the GPL-3.0, you should have received a copy
     * of said license along with the source code.
     *
     * If this is not the case or you just want to try ILIAS, you'll find
     * us at:
     *      https://www.ilias.de
     *      https://github.com/ILIAS-eLearning
     *
     *****************************************************************************/
    class SeqConditionSet
    {
        public ?string $mCombination = null;
        
        //convert vector to array
        public ?array $mConditions = null;
        public bool $mRetry = false;
        public bool $mRollup = false;
        
        public function __construct($iRollup)
        {
            $this->mRollup = $iRollup;
        }
    }
