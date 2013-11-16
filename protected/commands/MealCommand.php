<?php
class MealCommand extends CConsoleCommand
{
    public function actionOrder($daemon = false, $numberOfWorkers = 1)
    {
        $handler = function ()
        {
            $mealLogic = new ModelLogicMeal();
            $mealLogic->order();
        };
        if ($daemon) {
            ProcessHelper::masterWorkers($handler, $numberOfWorkers, 'submit_images_to_be_migrating');
        } else {
            try {
                $handler();
            } catch (ModelLogicNoTaskException $e) {
                echo "no task\n";
                return 0;
            } catch (Exception $e) {
                echo "failed: " . $e->getMessage() . "\n";
                return - 1;
            }
            echo "succeed.\n";
            return 0;
        }
    }
    public function actionReport($abort = FALSE)
    {
        try {
            $mealLogic = new ModelLogicMeal();
            $mealLogic->report($abort);
        } catch (ModelLogicNoTaskException $e) {
            echo "no task\n";
            return 0;
        } catch (Exception $e) {
            echo "failed: " . $e->getMessage() . "\n";
            return - 1;
        }
        echo "succeed.\n";
        return 0;
    }
}