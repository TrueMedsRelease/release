/**
 * Design17Chat — unit tests (дубли запросов / корректность поллинга)
 *
 * Run with:
 *   npm install --save-dev jest jest-environment-jsdom
 *   npx jest chat.test.js
 *
 * @jest-environment jsdom
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const chatSource = fs.readFileSync(path.join(__dirname, 'chat.js'), 'utf8');

function setupDom() {
  document.body.innerHTML = `
    <div class="js-chat-container">
      <div class="thread-chat js-chat-thread-wrap">
        <div class="thread-chat__messages js-chat-thread"></div>
      </div>
      <div class="thread-box js-chat-form">
        <textarea class="js-chat-input"></textarea>
        <button class="js-chat-submit" type="submit"></button>
      </div>
    </div>
    <div class="design17-toast" id="design17-toast" hidden></div>
  `;
}

function okResponse(body) {
  return Promise.resolve({
    ok: true,
    status: 200,
    json: () => Promise.resolve(body),
  });
}

function setupGlobals(fetchImpl) {
  window.routeChatSend = '/chat/send';
  window.routeChatPoll = '/chat/poll/__MSGID__';
  window.routeChatHistory = '/chat/history';
  window.medbotPollInterval = 5; // ускоряем поллинг для тестов
  window.design17ChatTexts = {};

  window.scrollTo = jest.fn();
  global.requestAnimationFrame = (cb) => setTimeout(cb, 0);
  Element.prototype.scrollIntoView = jest.fn();
  Element.prototype.scrollTo = jest.fn();
  global.fetch = jest.fn(fetchImpl);
}

function loadChat() {
  // chat.js — IIFE: исполняем в глобальном контексте jsdom
  const run = new Function(chatSource);
  run();
  return window.Design17Chat;
}

describe('Design17Chat: sendMessage', () => {
  let sendCalls;

  beforeEach(() => {
    jest.useFakeTimers();
    setupDom();

    const stats = { send: 0, polls: 0, inFlight: 0, maxInFlight: 0 };
    sendCalls = stats;

    setupGlobals((url) => {
      const u = String(url);
      stats.inFlight++;
      stats.maxInFlight = Math.max(stats.maxInFlight, stats.inFlight);

      let body = { success: false };
      if (u.indexOf('/chat/history') === 0) {
        body = { success: true, messages: [] };
      } else if (u.indexOf('/chat/send') === 0) {
        stats.send++;
        body = { success: true, message_id: 'm1' };
      } else if (u.indexOf('/chat/poll/') === 0) {
        stats.polls++;
        body =
          stats.polls === 1
            ? { success: true, status: 'queued' }
            : { success: true, status: 'done', answer: 'Answer', products: [] };
      }

      return okResponse(body).finally(() => {
        stats.inFlight--;
      });
    });

    loadChat();
  });

  afterEach(() => {
    jest.useRealTimers();
    document.body.innerHTML = '';
    delete global.fetch;
  });

  test('не отправляет /chat/send повторно при двойном вызове sendMessage', () => {
    const chat = window.Design17Chat;
    chat.sendMessage('hello');
    chat.sendMessage('hello'); // второй вызов должен быть заблокирован

    expect(sendCalls.send).toBe(1);
  });

  test('поллинг строго последовательный: ни один запрос не пересекается', async () => {
    const chat = window.Design17Chat;
    chat.sendMessage('hello');

    // ждём send -> startPolling -> poll#1 (queued) -> scheduleNextPoll
    await Promise.resolve();
    await Promise.resolve();
    await jest.advanceTimersByTimeAsync(5); // poll#2 (done)

    expect(sendCalls.polls).toBeGreaterThanOrEqual(2);
    expect(sendCalls.maxInFlight).toBe(1);
  });

  test('после done состояние возвращается в DONE (можно слать снова)', async () => {
    const chat = window.Design17Chat;
    chat.sendMessage('hello');

    await Promise.resolve();
    await Promise.resolve();
    await jest.advanceTimersByTimeAsync(5);

    expect(chat.getState()).toBe('done');
  });
});
