<?php

namespace App\Model;

use App\Localize\UITranslator;
use App\Service\Services;

class ItemPrize extends AbstractGame implements Game
{

    public function __construct(
        private ?Item $item,
        ?int          $gameId = null,
    )
    {
        parent::__construct($gameId);
        if ($item) {
            $this->bean['item_id'] = $item->getId();
        } else if ($this->bean['item_id']) {
            $this->item = new ItemModel($this->bean['item_id']);
        }
    }

    function getOfferText(UITranslator $translator): string
    {
        return $translator->composeItemText($this->item);
    }

    function accept(UITranslator $translator): string
    {
        Services::getDB()::exec('UPDATE item SET count = count - 1, hold = hold - 1 WHERE id = ?', [$this->bean['item_id']]);
        $this->deactivateGame();
        return $translator->composeItemSendText($this->item);
    }

    function decline(): void
    {
        Services::getDB()::exec('UPDATE item SET hold = hold - 1 WHERE id = ?', [$this->bean['item_id']]);
        parent::decline();
    }

}