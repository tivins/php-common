<?php

namespace Tivins\Core\Structure;

use JsonSerializable;

/**
 * Abstract model of tree using linked-list.
 */
class Node implements JsonSerializable
{
    const DIR_RIGHT = 'next';
    const DIR_LEFT  = 'previous';
    const DIR_UP    = 'parent';

    private static int $counter = 1;

    public readonly int $id;
    private ?Node $next = null ;
    private ?Node $previous = null ;
    private ?Node $first_child = null ;
    private ?Node $parent = null ;

    public function __construct()
    {
        $this->id = self::$counter++;
    }

    /**
     * Assign the given Node next to the current node.
     *
     * @param   ?Node $new_next
     *          The new node to attach to the right of the current node.
     *          A null value can be provided to unlink the next node.
     *
     * @return  ?Node
     *          Returns the current next node if exists or null otherwise.
     *
     * @todo    To create unit test case.
     * @group   Tree operation
     */
    public function set_next(?Node $new_next): ?Node
    {
        $unlinked = null;

        // If the current token has already a next token,
        // remove the `previous` link on it, and remove from its parent,
        // because it can't be found using `parent->first_child->next`.
        if ($this->next) {
            $this->next->previous = null;
            $this->next->parent = null;
            $unlinked = $this->next;
        }

        $this->next = $new_next;

        // Update properties of the next node (previous + parent).
        if (!is_null($new_next))
        {
            $this->next->previous = $this;
            $this->next->parent = $this->parent;
        }
        return $unlinked;
    }

    public function append_child(Node $new_child)
    {
        if (is_null($this->first_child)) {
            $this->first_child = $new_child ;
            $new_child->parent = $this ;
        }
    }

    public function get_last():Node
    {
        $last = $this ;
        while (true) {
            $last = $last->next ;
            if (!$last->next) return $last ;
        }
    }

    /**
     * Insert `new_child` as the first child of the current node.
     * The value of "previous" will be turned to null.
     *
     * @todo    To create unit test case.
     * @todo    Do return a boolean to check if node is inserted?
     */
    public function set_first_child(Node $new_child): void
    {
        assert(is_null($new_child->previous) && is_null($new_child->next));

        $new_child->parent = $this ;
        if (is_null($this->first_child)) {
            $this->first_child = $new_child ;
            $new_child->previous = null;
            return;
        }

        $second = $this->first_child ;
        $this->first_child = $new_child;
        $this->first_child->next = $second;
        $second->previous = $new_child;
    }

    /**
     * Remove the first child of the current node.
     *
     * @group   Tree operation
     * @return  ?Node the removed node or null if this node has no children.
     * @todo    To create unit test case.
     */
    public function remove_first_child(): ?Node
    {
        // no children, skip.
        if (is_null($this->first_child)) return null;

        $fc = $this->first_child;

        // Newly promoted first child :
        $this->first_child = $this->first_child->next;
        $this->first_child->previous = null;

        // defeated old 1st child
        $fc->parent = null;
        $fc->previous = null;
        $fc->next = null;
        return $fc;
    }

    /**
     * Remove the next node and attach to the left of the next-next node if
     * exists.
     *
     * Context: `<Current> <Node_Next|null> <Node_Next_Next|null>`
     * Purpose: To remove `<Node_Next>` and attach `<Current>`
     *          to `<Node_Next_Next>`
     *
     * @group   Tree operation
     * @return  ?Node Returns the removed token or null if no next.
     * @todo    To create unit test case.
     */
    public function remove_next(): ?Node
    {
        // no next, skip.
        if (is_null($this->next)) return null;

        // store removed node in variable to return it.
        $removed = $this->next;

        // Si Node_Next_Next, assign 'left' reference to this.
        if ($removed->next) $removed->next->previous = $this;

        // Assign 'right' of this to Node_Next_Next.
        $this->next = $removed->next;

        return $removed;
    }

    /**
     *
     * Context: `<Node_Previous|null> <Node_To_Remove|null> <Current>`
     * Purpose: Remove 'Node_To_Remove' and attach 'Current' and 'Node_Prev'
     *
     * @group   Tree operation
     * @return  ?Node Returns the removed token or null if no previous.
     */
    public function remove_previous(): ?Node
    {
        // no previous, skip.
        if (is_null($this->previous)) return null;

        // store removed node in variable to return it.
        $removed = $this->previous;

        // Si Node_Previous, assign 'right' reference to this.
        if ($removed->previous) $removed->previous->next = $this;

        // Assign 'left' of this to Node_Previous.
        $this->previous = $removed->previous;

        return $removed;
    }

    public function getNext(): ?Node
    {
        return $this->next;
    }

    public function getPrevious(): ?Node
    {
        return $this->previous;
    }

    public function getFirstChild(): ?Node
    {
        return $this->first_child;
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }



    public function __toString(): string
    {
        return '#' . $this->id;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'parent' => (int)$this->parent?->id,
            'first_child' => (int)$this->first_child?->id,
            'prev' => (int)$this->previous?->id,
            'next' => (int)$this->next?->id,
        ];
    }
}

/*
class Text_Offset {
    public $file = '' ;
    public $line = 0 ;
    public $column = 0 ;
    public function __construct(string $file = '', int $line = 0, int $column = 0) {
        $this->file = $file;
        $this->line = $line;
        $this->column = $column;
    }
}
*/
