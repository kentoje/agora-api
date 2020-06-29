/*dashboard info */
SELECT USER.id                                                 AS userID,
       USER.last_name,
       USER.first_name,
       USER.saving_water,
       USER.saving_gas,
       USER.saving_waste,
       USER.saving_electricity,
       USER.gas_average_consumption,
       USER.water_average_consumption,
       USER.waste_average_consumption,
       USER.electricity_average_consumption,
       mesure_user.mesure_gas,
       mesure_user.mesure_water,
       mesure_user.mesure_waste,
       mesure_user.mesure_electricity,
       level.level_number,
       level.reduction_rate,
       Period_diff(Date_format(Curdate(), "%y%m"),
       Date_format(USER.registration_date, "%y%m"))            AS
       nbMonthsRegistered,
       (SELECT Count(task.id)
        FROM   task
               INNER JOIN task_user
                       ON task.id = task_user.task_id
               INNER JOIN date
                       ON date.id = task.date_id
        WHERE  task_user.user_id = userid
               AND task.NAME = 'Eau'
               AND task.validate = 1
               AND date.date < Date_format(Now(), '%Y-%m-01')) AS
       nbValidatedTaskWater,
       (SELECT Count(task.id)
        FROM   task
               INNER JOIN task_user
                       ON task.id = task_user.task_id
               INNER JOIN date
                       ON date.id = task.date_id
        WHERE  task_user.user_id = userid
               AND task.NAME = 'Gaz'
               AND task.validate = 1
               AND date.date < Date_format(Now(), '%Y-%m-01')) AS
       nbValidatedTaskGas,
       (SELECT Count(task.id)
        FROM   task
               INNER JOIN task_user
                       ON task.id = task_user.task_id
               INNER JOIN date
                       ON date.id = task.date_id
        WHERE  task_user.user_id = userid
               AND task.NAME = 'Déchêts'
               AND task.validate = 1
               AND date.date < Date_format(Now(), '%Y-%m-01')) AS
       nbValidatedTaskWaste,
       (SELECT Count(task.id)
        FROM   task
               INNER JOIN task_user
                       ON task.id = task_user.task_id
               INNER JOIN date
                       ON date.id = task.date_id
        WHERE  task_user.user_id = userid
               AND task.NAME = 'Electricté'
               AND task.validate = 1
               AND date.date < Date_format(Now(), '%Y-%m-01')) AS
       nbValidatedTaskElec,
       (SELECT Count(task.id)
        FROM   task
               INNER JOIN task_user
                       ON task.id = task_user.task_id
               INNER JOIN date
                       ON date.id = task.date_id
        WHERE  task_user.user_id = userid
               AND task.validate = 1
               AND date.date >= Date_format(Now(), '%Y-01-01')
               AND date.date < Date_format(Now(), '%Y-%m-01'))
       nbValidateTaskInThisYear
FROM   USER
       INNER JOIN level
               ON USER.level_id = level.id
       INNER JOIN (SELECT mesure.to_mesure_id AS mesure_user_id,
                          mesure.gas          AS mesure_gas,
                          mesure.electricity  AS mesure_electricity,
                          mesure.water        AS mesure_water,
                          mesure.waste        AS mesure_waste
                   FROM   mesure
                          INNER JOIN date
                                  ON mesure.date_id = date.id
                   WHERE  date.date >= Date_format(Now(), '%Y-%m-01'))
                  mesure_user
               ON USER.id = mesure_user.mesure_user_id
WHERE  USER.id = 1 

/*dashboard list task*/
SELECT task.*
FROM   task
       INNER JOIN task_user
               ON task.id = task_user.task_id
       INNER JOIN USER
               ON task_user.user_id = USER.id
       INNER JOIN date
               ON task.date_id = date.id
WHERE  date.date >= Date_format(Now(), '%Y-%m-01')
       AND USER.id = 1

/*historique*/
SELECT date.date,
       mesure.water,
       mesure.electricity,
       mesure.gas,
       mesure.waste,
       mesure.navigo_subscription,
       USER.water_average_consumption,
       USER.electricity_average_consumption,
       USER.gas_average_consumption,
       USER.waste_average_consumption
FROM   mesure
       INNER JOIN date
               ON mesure.date_id = date.id
       INNER JOIN USER
               ON mesure.to_mesure_id = USER.id

/*analytiques*/
