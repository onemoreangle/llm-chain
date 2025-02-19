<?php

namespace PhpLlm\LlmChain\Bridge\Google;

use PhpLlm\LlmChain\Model\Message\AssistantMessage;
use PhpLlm\LlmChain\Model\Message\Content\Audio;
use PhpLlm\LlmChain\Model\Message\Content\ContentVisitor;
use PhpLlm\LlmChain\Model\Message\Content\Image;
use PhpLlm\LlmChain\Model\Message\Content\Text;
use PhpLlm\LlmChain\Model\Message\MessageBagInterface;
use PhpLlm\LlmChain\Model\Message\MessageVisitor;
use PhpLlm\LlmChain\Model\Message\SystemMessage;
use PhpLlm\LlmChain\Model\Message\ToolCallMessage;
use PhpLlm\LlmChain\Model\Message\UserMessage;
use PhpLlm\LlmChain\Platform\RequestBodyProducer;

final class GoogleRequestBodyProducer implements RequestBodyProducer, MessageVisitor, ContentVisitor, \JsonSerializable
{
    public function __construct(protected MessageBagInterface $bag)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function createBody(): array
    {
        $contents = [];
        foreach ($this->bag->withoutSystemMessage()->getMessages() as $message) {
            $contents[] = [
                'role' => $message->getRole(),
                'parts' => $message->accept($this),
            ];
        }

        $body = [
            'contents' => $contents,
        ];

        $systemMessage = $this->bag->getSystemMessage();
        if (null !== $systemMessage) {
            $body['systemInstruction'] = [
                'parts' => $systemMessage->accept($this),
            ];
        }

        return $body;
    }

    /**
     * @return array<array<string, string>>
     */
    public function visitUserMessage(UserMessage $message): array
    {
        $parts = [];
        foreach ($message->content as $content) {
            $parts[] = [...$content->accept($this)];
        }

        return $parts;
    }

    /**
     * @return array<array<string, string>>
     */
    public function visitAssistantMessage(AssistantMessage $message): array
    {
        return [['text' => $message->content]];
    }

    /**
     * @return array<array<string, string>>
     */
    public function visitSystemMessage(SystemMessage $message): array
    {
        return [['text' => $message->content]];
    }

    /**
     * @return string[]
     */
    public function visitText(Text $content): array
    {
        return ['text' => $content->text];
    }

    /**
     * @return string[]
     */
    public function visitImage(Image $content): array
    {
        // TODO: support image
        return [];
    }

    /**
     * @return string[]
     */
    public function visitAudio(Audio $content): array
    {
        // TODO: support audio
        return [];
    }

    /**
     * @return string[]
     */
    public function visitToolCallMessage(ToolCallMessage $message): array
    {
        // TODO: support tool call message
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->createBody();
    }
}
