<div class="ml-2">
    <div class="row mt-3">
        <div class="col-sm-1">
                      <span class="custom-radio float-xs-left">
                        <input name="envialia-radio" value="normal" type="radio">
                        <span></span>
                      </span>
        </div>
        <label class="col-sm-11">
            <div class="row">
                <div class="col-xs-12">
                    <span class="h6">Normal</span>
                </div>
            </div>
        </label>
    </div>

    <div class="row">
        <div class="col-sm-1">
                      <span class="custom-radio float-xs-left">
                        <input  name="envialia-radio" value="horario_concertado" type="radio">
                          <span></span>
                      </span>
        </div>
        <label class="col-sm-11">
            <div class="row">
                <div class="col-xs-12">
                    <span class="h6">Horario Concertado</span>
                    {*<div class="form-check d-inline">*}
                    {*<select name="mt" class="form-control d-inline p-0" style="width: auto; height: auto">*}
                    {*<option selected>Mañana</option>*}
                    {*<option value="1">Tarde</option>*}
                    {*</select>*}
                    {*</div>*}
                    <div class="form-check d-inline">
                        <select name="hour" class="form-control d-inline p-0" style="width: auto; height: auto">
                            <option selected value="0" >Hora</option>
                            <option value="9" >9:00 am</option>
                            <option value="10" >10:00 am</option>
                            <option value="11" >11:00 am</option>
                            <option value="12" >12:00 pm</option>
                            <option value="4" >4:00 pm</option>
                            <option value="5" >5:00 pm</option>
                        </select>
                    </div>
                    <p style="font-size: 12px;">
                        ( Plazo de horario mínimo 2 horas entre las franjas horarias de 9:00 a 14:00 y de 16:00 a 19:00 )
                    </p>
                </div>
            </div>
        </label>
    </div>
    <div class="row">
        <div class="col-sm-1">
                      <span class="custom-radio float-xs-left">
                        <input name="envialia-radio" value="franja_horaria" type="radio">
                        <span></span>
                      </span>
        </div>
        <label class="col-sm-11">
            <div class="row">
                <div class="col-xs-12">
                    <span class="h6">Franja Horaria (Mañana/Tarde)</span>
                    <div class="form-check d-inline">
                        <select name="mt" class="form-control d-inline p-0" style="width: auto; height: auto">
                            <option selected value="0">Seleccionar</option>
                            <option value="1">Mañana</option>
                            <option value="2">Tarde</option>
                        </select>
                    </div>
                    <p style="font-size: 12px">
                        ( Mañana, horario de entrega de 9:00 am a 2:00pm. Tarde, horario de entrega de 4:00 pm a 7:00pm )
                    </p>
                </div>
            </div>
        </label>
    </div>
</div>



