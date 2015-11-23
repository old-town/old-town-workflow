#OSWorkflow – Ваш первый workflow

Во-первых, давайте дадим определение нашему workflow. Вы можете назвать его как угодно. Определения рабочего процесса задаются в XML-файле, по одному один workflow на файл. Начнем с создания файла под названием "myworkflow.xml". Шаблон файла выглядит следующим образом:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC 
  "-//OpenSymphony Group//DTD OSWorkflow 2.8//EN"
  "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
  <initial-actions>
    ...
  </initial-actions>
  <steps>
    ...
  </steps>
</workflow>
```

Стандартный заголовок XML указывается первым. Обратите внимание, что OSWorkflow будет проверять все файлы XML на соответствие указанному DTD, поэтому определение workflow должен быть валидным. Вы можете редактировать его с помощью инструментов редактирования XML, и ошибки будут выделены соответствующим образом.

### Этапы (Steps) и действия (actions)

Далее мы задаем начальные действия и этапы. Первой концепцией, важной для понимания OSWorkflow, являются этапы и действия. Этап - это просто состояние workflow. По мере работы workflow, он переходит из одного этапа в другой (или иногда даже остается на том же этапе). Например, имена этапов для системы управления документами могут быть такими: «Черновик» (First Draft), «Этап редактирования» (Edit Stage) и «У издателя» (At publisher).

Действия указывают на переходы, которые могут иметь место в течение определенного этапа. Действие может часто приводить к изменению этапа. Примерами действий в нашей системе управления документами будет «начать работу с черновиком» (start first draft) и «закончить работу с черновиком» (complete first draft) на этапе «Черновик», указанном выше.

Проще говоря, этап – это «где мы», а действие – «куда мы можем идти».

Начальные действия (initial actions) представляют собой особый тип действий, которые используются для запуска workflow. В самом начале workflow, у нас нет никакого состояния, и мы не находимся ни на одном этапе. Пользователь должен предпринять некоторые действия, чтобы запустить workflow, и этот набор возможных действий для начала рабочего процесса указывается в <initial-actions>.

В нашем примере, давайте предположим, что у нас есть только одно начальное действие, «Запуск Workflow» (Start Workflow). Добавьте следующее определение действий внутри <initial-actions>:

```xml
<action id="1" name="Start Workflow">
  <results>
    <unconditional-result old-status="Finished" status="Queued" step="1"/>
  </results>
</action>
```

Это самый простой тип действия. В нем просто указывается, на какой этап мы переходим, а также, в какое значение установить состояние.

### Состояние workflow

Состояние workflow – это строка, которая описывает состояние workflow на конкретном этапе. В нашей системе управления документами этап «Черновик» может использовать два различных состояния «В работе» (Underway) и «В очереди» (Queued).

Мы используем состояние «В очереди», чтобы обозначить, что этот документ был поставлен в очередь на этапе «Черновик». Допустим, кто-то направил запрос на создание определенного документа, но исполнитель еще не был назначен. Таким образом документ находится «В очереди» на этапе «Черновик». Состояние «В работе» будет использоваться для обозначения ситуации, когда исполнитель взял документ из очередь и, возможно, заблокировал его, показав, что теперь он работает над черновиком. 

### Первый этап (Step)

Давайте посмотрим, как первый этап будет определяться в наших терминах. Мы знаем, что у нас есть два действия. Первое из них (начать работу с черновиком) не вызывает смены этапа, а меняет состояние на «В работе». Второе действие переводит нас на следующий этап в workflow, в данном случае – этап «Завершено». Таким образом, мы теперь добавим следующее в наш код:

```xml
<step id="1" name="First Draft">
  <actions>
    <action id="1" name="Start First Draft">
      <results>
        <unconditional-result old-status="Finished" status="Underway" step="1"/>
      </results>
    </action>
    <action id="2" name="Finish First Draft">
      <results>
        <unconditional-result old-status="Finished" status="Queued" step="2"/>
      </results>
    </action>
  </actions>
</step>
<step id="2" name="finished" />
```

Выше мы видим, что определены два действия. Атрибут old-status используется для обозначения того, что должно быть введено в таблице истории для текущего состояния, чтобы обозначить, что это состояние было изменено. Почти во всех случаях, это будет «Завершено».

Такие действия, как указано выше, имеют ограниченное применение. Например, пользователь может вызвать действие 2 без предварительного вызова действия 1. Очевидно, что нельзя завершить работу с черновиком, если эта работа еще не начата. Точно так же можно начать работу несколько раз, что тоже не имеет смысла. Наконец, еще мы не можем запретить второму пользователю завершать работу с черновиком первого пользователя, а этой ситуации хотелось бы избежать.

Будем решать эти проблемы по одной. Во-первых, мы хотели бы указать, что пользователь, вызывающий действие, может начать работу с черновиком, когда workflow находится в состоянии «В очереди». Это не даст пользователям начинать работу несколько раз. Для этого мы указываем ограничение на действие. Ограничение состоит из условия.

### Условия (Conditions)

OSWorkflow имеет ряд полезных встроенных условий, которыми мы можем воспользоваться. В нашем случае нужное условие – «StatusCondition». Условия могут также принимать аргументы, как правило, указанные в Phpdocs для конкретного условия (если это условие реализуется как класс PHP).

Условия, как функции и другие базовые конструкции, могут быть реализованы различными способами, в том числе через PhpShell-скрипты или классы PHP, которые реализуют интерфейс Condition.

Например, в нашем случае мы используем класс условия состояния. Условие состояния принимает аргумент «status», которые указывает на состояние, которое нужно проверить, чтобы условие сработало. Смысл этой конструкции становится понятнее, если мы рассмотрим XML для этого условия:

```xml
<action id="1" name="Start First Draft">
  <restrict-to>
    <conditions>
      <condition type="class">
        <arg name="class.name">OldTown\Workflow\Util\StatusCondition</arg>
        <arg name="status">Queued</arg>
      </condition>
    </conditions>
  </restrict-to>
  <results>
    <unconditional-result old-status="Finished" status="Underway" step="1"/>
  </results>
</action>
```

Надеемся, так стало понятнее. Вышеуказанное условие не позволяет выполняться действию 1, если текущее состояние не равно «В очереди», а это состояние задается после выполнения начального действия.

### Функции (Functions)

Далее, мы хотели бы указать, что, когда пользователь начинает работу с черновиком, он становится «владельцем» (owner). Для этого нам нужны следующие вещи:

1. Функция, которая заполняет переменную «вызывающий» (caller) в текущем контексте.
2. 2. Установка атрибута «владелец» (owner) результатом переменной «вызывающий».

Функции – это мощная особенность OSWorkflow. По сути, функция - это единица работы, которая может быть выполнена во время перехода workflow и которая не влияет на сам workflow. Например, мы могли бы иметь функцию «SendEmail», которая отвечает за отправку уведомления по электронной почте в момент, когда происходит конкретный переход.

Функции могут также добавлять переменные в текущий контекст. Переменная - это именованный объект, который становится доступен в workflow и к которому могут обращаться другие функции или скрипты.

OSWorkflow поставляется с рядом полезных встроенных функций. Одна из этих функций – это функция «Caller». Эта функция ищет текущего пользователя, вызывающего workflow, и публикует переменную под названием caller («вызывающий») со строковым значением вызывающего пользователя.

Так как мы хотели бы, чтобы отслеживать, кто начал работу с нашим черновиком, мы воспользуемся этой функцией, изменив наше действие следующим образом:

```xml
<action id="1" name="Start First Draft">
  <pre-functions>
    <function type="class">
      <arg name="class.name">OldTown\Workflow\Util\Caller</arg>
    </function>
  </pre-functions>
  <results>
    <unconditional-result old-status="Finished" status="Underway" 
                                       step="1" owner="${caller}"/>
  </results>
</action>
```

### Окончательная сборка

Собрав все концепции, описанные выше, вместе, мы получаем следующее описание для действия 1:

```xml
<action id="1" name="Start First Draft">
  <restrict-to>
    <conditions>
      <condition type="class">
        <arg name="class.name">OldTown\Workflow\Util\StatusCondition</arg>
        <arg name="status">Queued</arg>
      </condition>
    </conditions>
  </restrict-to>
  <pre-functions>
    <function type="class">
      <arg name="class.name">OldTown\Workflow\Util\Caller</arg>
    </function>
  </pre-functions>
  <results>
    <unconditional-result old-status="Finished" status="Underway" 
                                       step="1"  owner="${caller}"/>
  </results>
</action>
```

Используем те же концепции для действия 2:

```xml
<action id="2" name="Finish First Draft">
  <restrict-to>
    <conditions type="AND">
      <condition type="class">
        <arg name="class.name">OldTown\Workflow\Util\StatusCondition</arg>
        <arg name="status">Underway</arg>
      </condition>
      <condition type="class">
        <arg name="class.name">OldTown\Workflow\Util\AllowOwnerOnlyCondition</arg>
      </condition>
    </conditions>
  </restrict-to>
  <results>
    <unconditional-result old-status="Finished" status="Queued" step="2"/>
  </results>
</action>
```

Здесь мы указываем новое условие - «только для владельца» (allow owner only). Оно гарантирует, что только пользователь, который начал работу с черновиком, может закончить его (что мы указали в атрибуте владельца предыдущего результата). Условие состояния также гарантирует, что действие «закончить работу с черновиком» можно выполнить только тогда, когда статус «В работе», что происходит только после того, как пользователь взял в работу черновик.

Таким образом, мы получаем окончательное определение workflow:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC 
                 "-//OpenSymphony Group//DTD OSWorkflow 2.8//EN"
                 "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
  <initial-actions>
    <action id="1" name="Start Workflow">
      <results>
        <unconditional-result old-status="Finished" status="Queued" step="1"/>
      </results>
    </action>
  </initial-actions>
  <steps>
    <step id="1" name="First Draft">
      <actions>
        <action id="1" name="Start First Draft">
          <restrict-to>
            <conditions>
              <condition type="class">
                <arg name="class.name">OldTown\Workflow\Util\StatusCondition</arg>
                <arg name="status">Queued</arg>
              </condition>
            </conditions>
          </restrict-to>
          <pre-functions>
            <function type="class">
              <arg name="class.name">OldTown\Workflow\Util\Caller</arg>
            </function>
          </pre-functions>
          <results>
            <unconditional-result old-status="Finished" status="Underway" 
                                           step="1"  owner="${caller}"/>
          </results>
        </action>
        <action id="2" name="Finish First Draft">
          <restrict-to>
            <conditions type="AND">
              <condition type="class">
                <arg name="class.name">OldTown\Workflow\Util\StatusCondition</arg>
                <arg name="status">Underway</arg>
              </condition>
              <condition type="class">
                <arg name="class.name">OldTown\Workflow\Util\AllowOwnerOnlyCondition</arg>
              </condition>
            </conditions>
          </restrict-to>
          <results>
            <unconditional-result old-status="Finished" status="Queued" step="2"/>
          </results>
        </action>
      </actions>
    </step>
    <step id="2" name="finished" />
  </steps>
</workflow>
```

Теперь, когда определение workflow завершено, время протестировать его поведение.

Перейти к [Тестирование вашего workflow](testing_your_workflow.md).
