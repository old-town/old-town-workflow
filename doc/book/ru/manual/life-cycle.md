# Жизненный цикл OTWorkflow

Переход между двумя состояниями (doAction). Вариант когда нет разделения и слияние процесса (нет join'ов и split'ов)

------------------------------------------------------------------------------------------------------------------
Порядок|Действие                                                                               |Описание
-------|---------------------------------------------------------------------------------------|-----------------------------------------------------------
1      | actions['restrict-to'] && global-actions['restrict-to']                               | Проверка для получения списка действий которые могут быть выполнены. Проверяются global-actions и action текущего шага
2      | action['validators']                                                                  | Проверка что можно выполнить заданное действие. (Если валидация не проходит Exception)
3      | currentStep['post-function']                                                          | Вызов post-functions у текущего шага
4      | action['pre-function']                                                                | Вызов pre-function у текущего действия
5      | results['result']['valisators'] || results['unconditional-result']['valisators']      | Проверка корректности внешних условий, для выполнения условного или безусловного перехода (Берется первый переход удовлетворяющих conditions)
6      | results['result']['pre-function'] && results['unconditional-result']['pre-function']  | Вызов pre-function у условного или безусловного перехода (Берется первый переход удовлетворяющих conditions). 
7      | createNewCurrentStep                                                                  | Если шаг в который перешли не финальный, то создаем новый шаг
8      | newStep['pre-function']                                                               | Если перешли в ноый шаг, то для него выполняем pre-function
9      | results['result']['post-function'] && results['unconditional-result']['post-function']| Вызов post-function у условного или безусловного перехода (Берется первый переход удовлетворяющих conditions).
10     | action['post-function']                                                               | Вызов pre-function у текущего действия
11     | completeEntry                                                                         | Если действие помеченно как финальное, то финализируем процесс wf
12     | availableAutoActions                                                                  | Если у newStep, есть автоматически выполняемые действия, то осуществляем переход по ним