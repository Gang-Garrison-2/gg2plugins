object_event_add(Character,ev_step,ev_step_normal,"
    if instance_exists(player){
        gravity = .5;
gravity_direction = 90; 
        }
");