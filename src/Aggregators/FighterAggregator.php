<?php

namespace Cable8mm\MmaScrapers\Aggregators;

use Cable8mm\MmaScrapers\DTO\FighterDTO;

/*
* FighterAggregator
*
* - 역할: 두 FighterDTO를 병합하여 하나의 완전한 FighterDTO 생성
* - 주요 기능: 동일 인물 판단, 값 선택 전략, 이름 유사도 판단
*
* 병합 규칙:
* - 동일 인물 판단: sherdogId > 이름 유사도
* - 값 선택 전략: Sherdog 우선, 그 외는 null이 아닌 값 선택
* - 이름 유사도: 완전 일치 > 포함 관계 > Levenshtein 거리 <= 2
*
* @author Samgu Lee <cable8mm@gmail.com>
* @date 2026-04-21
* @license MIT
*/
class FighterAggregator
{
    public function merge(FighterDTO $a, FighterDTO $b): FighterDTO
    {
        // 1️⃣ 동일 인물인지 판단
        if (!$this->isSameFighter($a, $b)) {
            throw new \InvalidArgumentException('Different fighters');
        }

        return new FighterDTO(
            name: $this->pick($a->name, $b->name),
            nickname: $this->pick($a->nickname, $b->nickname),
            instagram: $this->pick($a->instagram, $b->instagram),
            teamname: $this->pick($a->teamname, $b->teamname),
            height: $this->pick($a->height, $b->height),
            win: $this->pick($a->win, $b->win),
            lose: $this->pick($a->lose, $b->lose),
            draw: $this->pick($a->draw, $b->draw),
            sherdogId: $this->pick($a->sherdogId, $b->sherdogId),
        );
    }

    /**
     * 동일 파이터 판단
     */
    public function isSameFighter(FighterDTO $a, FighterDTO $b): bool
    {
        // 1️⃣ sherdogId가 있으면 최우선
        if ($a->sherdogId && $b->sherdogId) {
            return $a->sherdogId === $b->sherdogId;
        }

        // 2️⃣ 이름 기반 fallback
        return $this->isSimilarName($a->name, $b->name);
    }

    /**
     * 값 선택 전략
     */
    private function pick(mixed $a, mixed $b): mixed
    {
        // Sherdog 우선 (b가 Sherdog이라고 가정)
        return $b ?? $a;
    }

    /**
     * 이름 유사도 판단 (핵심)
     */
    private function isSimilarName(string $a, string $b): bool
    {
        $a = $this->normalizeName($a);
        $b = $this->normalizeName($b);

        // 완전 일치
        if ($a === $b) {
            return true;
        }

        // 포함 관계
        if (str_contains($a, $b) || str_contains($b, $a)) {
            return true;
        }

        // levenshtein 거리
        return levenshtein($a, $b) <= 2;
    }

    /**
     * 이름 정규화
     */
    private function normalizeName(string $name): string
    {
        $name = mb_strtolower($name);

        // 공백 제거
        $name = preg_replace('/\s+/', '', $name);

        // 특수문자 제거
        $name = preg_replace('/[^a-z0-9가-힣]/u', '', $name);

        return $name;
    }
}
