<?php

/**
 * Biblioteca de acceso y métodos WebService
 * @author      miguel.cejas
 * @date        08/02/2017
 */
define('CONST_PA_TAR_FIJA', "CREATE PROCEDURE PA_GRABA_TARIFA_FIJA()
														BEGIN
															DECLARE d_precio DECIMAL(20,6);
															DECLARE d_man_pedido DECIMAL(20,6);
															DECLARE d_gasto_envio DECIMAL(20,6);
															DECLARE id_serv_grat_pen INT(10);
															DECLARE id_serv_grat_int INT(10);
															DECLARE d_imp_min_grat_pen DECIMAL(20,6);
															DECLARE d_imp_min_grat_int DECIMAL(20,6);

															delete rp from " . _DB_PREFIX_ . "range_price rp 
															left join " . _DB_PREFIX_ . "carrier c 
																on c.id_carrier = rp.id_carrier
															where c.external_module_name = 'envialiacarrier'
																or rp.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

															delete d from " . _DB_PREFIX_ . "delivery d 
															left join " . _DB_PREFIX_ . "carrier c 
																on c.id_carrier = d.id_carrier
															where c.external_module_name = 'envialiacarrier'
																or d.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

															delete cz from " . _DB_PREFIX_ . "carrier_zone cz 
															left join " . _DB_PREFIX_ . "carrier c 
																on c.id_carrier = cz.id_carrier
															where c.external_module_name = 'envialiacarrier'
																or cz.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

															select @d_man_pedido := cast(value AS DECIMAL(20,6))
															from " . _DB_PREFIX_ . "configuration
															where name = 'envialia_tar_imp_mani'; 

															select @d_gasto_envio := cast(value AS DECIMAL(20,6))
															from " . _DB_PREFIX_ . "configuration
															where name = 'envialia_tar_marg'; 

															insert into " . _DB_PREFIX_ . "range_price (id_carrier, delimiter1, delimiter2) 
															select id_carrier, 0, 999999 
															from " . _DB_PREFIX_ . "carrier where external_module_name = 'envialiacarrier';

															select @d_precio := cast(value AS DECIMAL(20,6))
															from " . _DB_PREFIX_ . "configuration
															where name = 'envialia_tar_imp_fijo';

															set @d_precio = @d_precio + @d_man_pedido;
									 
															if @d_gasto_envio > 0 then 
																set @d_precio = (@d_precio) + (@d_precio * @d_gasto_envio / 100);
															end if;

															insert into " . _DB_PREFIX_ . "carrier_zone (id_carrier, id_zone)
															select c.id_carrier, z.id_zone
															from " . _DB_PREFIX_ . "carrier c
															cross join " . _DB_PREFIX_ . "zone z
															inner join " . _DB_PREFIX_ . "envialia_zonas ez
															on ez.id_zone = z.id_zone
															inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
															on ets.ID_CARRIER = c.id_carrier
															where c.external_module_name = 'envialiacarrier'
																and (ets.T_INT = 0)
																and (ets.T_EUR = 0)
																and (ez.t_europa = 0)
																and (ez.t_inter = 0)
																
															union

															select c.id_carrier, z.id_zone
															from " . _DB_PREFIX_ . "carrier c
															cross join " . _DB_PREFIX_ . "zone z
															inner join " . _DB_PREFIX_ . "envialia_zonas ez
															on ez.id_zone = z.id_zone
															inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
															on ets.ID_CARRIER = c.id_carrier
															where c.external_module_name = 'envialiacarrier'
																and (ets.T_EUR = 1)
																and (ez.t_europa = 1)
																and (ez.t_inter = 0)
																
															union

															select c.id_carrier, z.id_zone
															from " . _DB_PREFIX_ . "carrier c
															cross join " . _DB_PREFIX_ . "zone z
															inner join " . _DB_PREFIX_ . "envialia_zonas ez
															on ez.id_zone = z.id_zone
															inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
															on ets.ID_CARRIER = c.id_carrier
															where c.external_module_name = 'envialiacarrier'
																and (ets.T_EUR = 1)
																and (ez.t_europa = 1)
																and (ets.T_INT = 1)
																and (ez.t_inter = 1);

															insert into " . _DB_PREFIX_ . "delivery (id_carrier, id_range_price, id_zone, price)
															select c.id_carrier, rp.id_range_price, z.id_zone, @d_precio 
															from " . _DB_PREFIX_ . "carrier c
															left join " . _DB_PREFIX_ . "range_price rp
																on c.id_carrier = rp.id_carrier
															cross join " . _DB_PREFIX_ . "zone z
															left join " . _DB_PREFIX_ . "envialia_zonas ez
																on ez.id_zone = z.id_zone
															inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
																on ets.ID_CARRIER = c.id_carrier
															where c.external_module_name = 'envialiacarrier'
																and (ets.T_INT = 0)
																and (ets.T_EUR = 0)
																and (ez.t_europa = 0)
																and (ez.t_inter = 0)
																
															union

															select c.id_carrier, rp.id_range_price, z.id_zone, @d_precio 
															from " . _DB_PREFIX_ . "carrier c
															left join " . _DB_PREFIX_ . "range_price rp
																on c.id_carrier = rp.id_carrier
															cross join " . _DB_PREFIX_ . "zone z
															left join " . _DB_PREFIX_ . "envialia_zonas ez
																on ez.id_zone = z.id_zone
															inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
																on ets.ID_CARRIER = c.id_carrier
															where c.external_module_name = 'envialiacarrier'
																and (ets.T_EUR = 1)
																and (ez.t_europa = 1)
																and (ez.t_inter = 0)
																
															union

															select c.id_carrier, rp.id_range_price, z.id_zone, @d_precio 
															from " . _DB_PREFIX_ . "carrier c
															left join " . _DB_PREFIX_ . "range_price rp
																on c.id_carrier = rp.id_carrier
															cross join " . _DB_PREFIX_ . "zone z
															left join " . _DB_PREFIX_ . "envialia_zonas ez
																on ez.id_zone = z.id_zone
															inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
																on ets.ID_CARRIER = c.id_carrier
															where c.external_module_name = 'envialiacarrier'
																and (ets.T_INT = 1)
																and (ets.T_EUR = 1)
																and (ez.t_europa = 1)
																and (ez.t_inter = 1);

															if (select ifnull(value, 0) from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_pen') = 1 then
																select @id_serv_grat_pen := ID_CARRIER from " . _DB_PREFIX_ . "envialia_tipo_serv 
																where V_COD_TIPO_SERV = (select value from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_pen_tipo_serv');

																select @d_imp_min_grat_pen := ifnull(value, 0) from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_pen_imp_min';

																delete from " . _DB_PREFIX_ . "delivery 
																where id_carrier = @id_serv_grat_pen;

																update " . _DB_PREFIX_ . "range_price set delimiter2 = @d_imp_min_grat_pen
																where id_carrier = @id_serv_grat_pen;

																insert into " . _DB_PREFIX_ . "range_price (id_carrier, delimiter1, delimiter2) 
																values (@id_serv_grat_pen, @d_imp_min_grat_pen, 999999);

																insert into " . _DB_PREFIX_ . "delivery (id_carrier, id_range_price, id_zone, price)
																select @id_serv_grat_pen, rp.id_range_price, z.id_zone, CASE WHEN rp.delimiter2 > @d_imp_min_grat_pen THEN 0 ELSE @d_precio END 
																from " . _DB_PREFIX_ . "range_price rp
																cross join " . _DB_PREFIX_ . "zone z
																left join " . _DB_PREFIX_ . "envialia_zonas ez
																	on ez.id_zone = z.id_zone
																where rp.id_carrier = @id_serv_grat_pen
																	and ez.t_inter = 0
																	and ez.t_europa = 0;
															end if;

															if (select ifnull(value, 0) from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_int') = 1 then
																select @id_serv_grat_int := ID_CARRIER from " . _DB_PREFIX_ . "envialia_tipo_serv 
																where V_COD_TIPO_SERV = (select value from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_int_tipo_serv');

																select @d_imp_min_grat_int := ifnull(value, 0) from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_int_imp_min';

																delete from " . _DB_PREFIX_ . "delivery 
																where id_carrier = @id_serv_grat_int;

																update " . _DB_PREFIX_ . "range_price set delimiter2 = @d_imp_min_grat_int
																where id_carrier = @id_serv_grat_int;

																insert into " . _DB_PREFIX_ . "range_price (id_carrier, delimiter1, delimiter2) 
																values (@id_serv_grat_int, @d_imp_min_grat_int, 999999);

																insert into " . _DB_PREFIX_ . "delivery (id_carrier, id_range_price, id_zone, price)
																select @id_serv_grat_int, rp.id_range_price, z.id_zone, CASE WHEN rp.delimiter2 > @d_imp_min_grat_int THEN 0 ELSE @d_precio END 
																from " . _DB_PREFIX_ . "range_price rp
																cross join " . _DB_PREFIX_ . "zone z
																left join " . _DB_PREFIX_ . "envialia_zonas ez
																	on ez.id_zone = z.id_zone
																where rp.id_carrier = @id_serv_grat_int
																	and ez.t_europa = 1;
															end if;
													 END;");

define('CONST_PA_TAR_PESO', "CREATE PROCEDURE PA_GRABA_TARIFA_PESO()
												BEGIN
													DECLARE d_peso_aux DECIMAL(20,6);  
													DECLARE d_man_pedido DECIMAL(20,6);
													DECLARE d_gasto_envio DECIMAL(20,6);
													DECLARE d_precio_aux DECIMAL(20,6);

													set d_peso_aux = 0;
													set d_precio_aux = 0;
													
													delete rp from " . _DB_PREFIX_ . "range_price rp 
													left join " . _DB_PREFIX_ . "carrier c 
													on c.id_carrier = rp.id_carrier
													where c.external_module_name = 'envialiacarrier'
													  or rp.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

													delete rw from " . _DB_PREFIX_ . "range_weight rw 
													left join " . _DB_PREFIX_ . "carrier c 
													on c.id_carrier = rw.id_carrier
													where c.external_module_name = 'envialiacarrier'
													  or rw.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

													delete d from " . _DB_PREFIX_ . "delivery d 
													left join " . _DB_PREFIX_ . "carrier c 
													on c.id_carrier = d.id_carrier
													where c.external_module_name = 'envialiacarrier'
													  or d.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

													delete cz from " . _DB_PREFIX_ . "carrier_zone cz 
													left join " . _DB_PREFIX_ . "carrier c 
													on c.id_carrier = cz.id_carrier
													where c.external_module_name = 'envialiacarrier';
													
													select @d_man_pedido := cast(value AS DECIMAL(20,6))
													from " . _DB_PREFIX_ . "configuration
													where name = 'envialia_tar_imp_mani'; 

													select @d_gasto_envio := cast(value AS DECIMAL(20,6))
													from " . _DB_PREFIX_ . "configuration
													where name = 'envialia_tar_marg'; 
													
													insert into " . _DB_PREFIX_ . "carrier_zone (id_carrier, id_zone)													
													select c.id_carrier, z.id_zone
													from " . _DB_PREFIX_ . "carrier c
													cross join " . _DB_PREFIX_ . "zone z
													inner join " . _DB_PREFIX_ . "envialia_zonas ez
													on ez.id_zone = z.id_zone
													inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
													on ets.ID_CARRIER = c.id_carrier
													where c.external_module_name = 'envialiacarrier'
														and (ets.T_INT = 0)
														and (ets.T_EUR = 0)
														and (ez.t_europa = 0)
														and (ez.t_inter = 0)
														
													union

													select c.id_carrier, z.id_zone
													from " . _DB_PREFIX_ . "carrier c
													cross join " . _DB_PREFIX_ . "zone z
													inner join " . _DB_PREFIX_ . "envialia_zonas ez
													on ez.id_zone = z.id_zone
													inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
													on ets.ID_CARRIER = c.id_carrier
													where c.external_module_name = 'envialiacarrier'
														and (ets.T_EUR = 1)
														and (ez.t_europa = 1)
														and (ez.t_inter = 0)
														
													union

													select c.id_carrier, z.id_zone
													from " . _DB_PREFIX_ . "carrier c
													cross join " . _DB_PREFIX_ . "zone z
													inner join " . _DB_PREFIX_ . "envialia_zonas ez
													on ez.id_zone = z.id_zone
													inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
													on ets.ID_CARRIER = c.id_carrier
													where c.external_module_name = 'envialiacarrier'
														and (ets.T_EUR = 1)
														and (ez.t_europa = 1)
														and (ets.T_INT = 1)
														and (ez.t_inter = 1);
														
													BLOCK1: begin
														DECLARE v_tipo_serv_cur VARCHAR(3);
														DECLARE no_more_rows_1 BOOLEAN; 
														DECLARE curServ CURSOR FOR select distinct(V_COD_TIPO_SERV) from " . _DB_PREFIX_ . "envialia_tarifa_pesos;
														DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows_1 = TRUE;
														SET no_more_rows_1 = FALSE;
														OPEN curServ;
													
														read_loop: LOOP
															FETCH curServ INTO v_tipo_serv_cur;    
															IF no_more_rows_1 THEN
															CLOSE curServ;
															LEAVE read_loop;
														END IF;      
															
														BLOCK2: begin
														DECLARE d_peso_cur DECIMAL(20,6);
														DECLARE id_carrier_cur INT(10);
														DECLARE no_more_rows_2 BOOLEAN;   
														DECLARE curRango CURSOR FOR  select distinct(etp.F_PESO), ets.id_carrier 
																					 from " . _DB_PREFIX_ . "envialia_tarifa_pesos etp 
																					 left join " . _DB_PREFIX_ . "envialia_tipo_serv ets
																						 on ets.V_COD_TIPO_SERV = etp.V_COD_TIPO_SERV 
																					 where etp.V_COD_TIPO_SERV = v_tipo_serv_cur
																					 order by etp.F_PESO asc;
																DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows_2 = TRUE;
														SET no_more_rows_2 = FALSE;
														OPEN curRango;
															read_loop_2: LOOP 
																	FETCH curRango INTO d_peso_cur, id_carrier_cur;      
																	IF no_more_rows_2 THEN
																	CLOSE curRango;
																	LEAVE read_loop_2;
																END IF;              
																	
																	IF d_peso_cur > d_peso_aux THEN
															insert into " . _DB_PREFIX_ . "range_weight(id_carrier, delimiter1, delimiter2) values (id_carrier_cur, d_peso_aux, d_peso_cur);
																		
															END IF;  

															SET d_peso_aux = d_peso_cur;
																	
															END LOOP read_loop_2;
																set d_peso_aux= 0;
															END BLOCK2;
														END LOOP read_loop; 
													END BLOCK1;
													
													BLOCK3: begin
													DECLARE id_carrier_cur INT(10);
														DECLARE id_zona_cur INT(10);
														DECLARE d_peso_cur DECIMAL(20,6);
														DECLARE d_precio_cur DECIMAL(20,6);
														DECLARE no_more_rows_3 BOOLEAN; 
														DECLARE curTar CURSOR FOR select ets.id_carrier, etp.I_COD_ZONA, etp.F_PESO, etp.F_PRECIO 
																											from " . _DB_PREFIX_ . "envialia_tarifa_pesos etp 
																				inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
																					on ets.V_COD_TIPO_SERV = etp.V_COD_TIPO_SERV 
																				order by etp.V_COD_TIPO_SERV, etp.I_COD_ZONA, etp.F_PESO;
																											
														DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows_3 = TRUE;
														SET no_more_rows_3 = FALSE;
														OPEN curTar;
														
														read_loop_3: LOOP
															FETCH curTar INTO id_carrier_cur, id_zona_cur, d_peso_cur, d_precio_cur;    
															IF no_more_rows_3 THEN
															CLOSE curTar;
															LEAVE read_loop_3;
														END IF; 
															
															set d_precio_aux = d_precio_cur;
															set d_precio_aux = d_precio_aux + @d_man_pedido;
															
														IF @d_gasto_envio > 0 then 
														set d_precio_aux = (d_precio_aux) + (d_precio_aux * @d_gasto_envio / 100);
														END IF;
															
															insert into " . _DB_PREFIX_ . "delivery(id_carrier, id_range_weight, id_zone, price) 
														select id_carrier_cur, rw.id_range_weight, id_zona_cur, d_precio_aux 
														from " . _DB_PREFIX_ . "range_weight rw
														where rw.delimiter2 <= d_peso_cur
														and rw.id_carrier = id_carrier_cur
														and rw.id_range_weight not in (select d2.id_range_weight from " . _DB_PREFIX_ . "delivery d2 
																													 where d2.id_carrier = id_carrier_cur
                                                             and d2.id_zone = id_zona_cur);
																
														set d_precio_aux = 0;
														END LOOP read_loop_3;    
													END BLOCK3;
													
													update " . _DB_PREFIX_ . "configuration set value = '0' where name = 'envialia_conf_tar';
													update " . _DB_PREFIX_ . "carrier set shipping_method = 1 where external_module_name = 'envialiacarrier';
												END;");
												
	define('CONST_PA_TAR_IMPO', "CREATE PROCEDURE PA_GRABA_TARIFA_IMPORTE()
												BEGIN
													DECLARE d_impor_aux DECIMAL(20,6);  
													DECLARE d_man_pedido DECIMAL(20,6);
													DECLARE d_gasto_envio DECIMAL(20,6);
													DECLARE d_precio_aux DECIMAL(20,6);
													
													set d_impor_aux = 0;
													set d_precio_aux = 0;
													
													delete rp from " . _DB_PREFIX_ . "range_price rp 
													left join " . _DB_PREFIX_ . "carrier c 
													on c.id_carrier = rp.id_carrier
													where c.external_module_name = 'envialiacarrier'
													  or rp.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

													delete rw from " . _DB_PREFIX_ . "range_weight rw 
													left join " . _DB_PREFIX_ . "carrier c 
													on c.id_carrier = rw.id_carrier
													where c.external_module_name = 'envialiacarrier'
													  or rw.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

													delete d from " . _DB_PREFIX_ . "delivery d 
													left join " . _DB_PREFIX_ . "carrier c 
													on c.id_carrier = d.id_carrier
													where c.external_module_name = 'envialiacarrier'
													  or d.id_carrier not in(select c2.id_carrier from " . _DB_PREFIX_ . "carrier c2);

													delete cz from " . _DB_PREFIX_ . "carrier_zone cz 
													left join " . _DB_PREFIX_ . "carrier c 
													on c.id_carrier = cz.id_carrier
													where c.external_module_name = 'envialiacarrier';
													
													select @d_man_pedido := cast(value AS DECIMAL(20,6))
													from " . _DB_PREFIX_ . "configuration
													where name = 'envialia_tar_imp_mani'; 

													select @d_gasto_envio := cast(value AS DECIMAL(20,6))
													from " . _DB_PREFIX_ . "configuration
													where name = 'envialia_tar_marg'; 
													
													insert into " . _DB_PREFIX_ . "carrier_zone (id_carrier, id_zone)													
													select c.id_carrier, z.id_zone
													from " . _DB_PREFIX_ . "carrier c
													cross join " . _DB_PREFIX_ . "zone z
													inner join " . _DB_PREFIX_ . "envialia_zonas ez
													on ez.id_zone = z.id_zone
													inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
													on ets.ID_CARRIER = c.id_carrier
													where c.external_module_name = 'envialiacarrier'
														and (ets.T_INT = 0)
														and (ets.T_EUR = 0)
														and (ez.t_europa = 0)
														and (ez.t_inter = 0)
														
													union

													select c.id_carrier, z.id_zone
													from " . _DB_PREFIX_ . "carrier c
													cross join " . _DB_PREFIX_ . "zone z
													inner join " . _DB_PREFIX_ . "envialia_zonas ez
													on ez.id_zone = z.id_zone
													inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
													on ets.ID_CARRIER = c.id_carrier
													where c.external_module_name = 'envialiacarrier'
														and (ets.T_EUR = 1)
														and (ez.t_europa = 1)
														and (ez.t_inter = 0)
														
													union

													select c.id_carrier, z.id_zone
													from " . _DB_PREFIX_ . "carrier c
													cross join " . _DB_PREFIX_ . "zone z
													inner join " . _DB_PREFIX_ . "envialia_zonas ez
													on ez.id_zone = z.id_zone
													inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
													on ets.ID_CARRIER = c.id_carrier
													where c.external_module_name = 'envialiacarrier'
														and (ets.T_EUR = 1)
														and (ez.t_europa = 1)
														and (ets.T_INT = 1)
														and (ez.t_inter = 1);
														
													BLOCK1: begin
														DECLARE v_tipo_serv_cur VARCHAR(3);
														DECLARE no_more_rows_1 BOOLEAN; 
														DECLARE curServ CURSOR FOR select distinct(V_COD_TIPO_SERV) from " . _DB_PREFIX_ . "envialia_tarifa_importes;
														DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows_1 = TRUE;
														SET no_more_rows_1 = FALSE;
														OPEN curServ;
													
														read_loop: LOOP
															FETCH curServ INTO v_tipo_serv_cur;    
															IF no_more_rows_1 THEN
															CLOSE curServ;
															LEAVE read_loop;
														END IF;      
															
														BLOCK2: begin
														DECLARE d_impor_cur DECIMAL(20,6);
														DECLARE id_carrier_cur INT(10);
														DECLARE no_more_rows_2 BOOLEAN;   
														DECLARE curRango CURSOR FOR  select distinct(eti.F_IMPORTE), ets.id_carrier 
																					 from " . _DB_PREFIX_ . "envialia_tarifa_importes eti 
																					 left join " . _DB_PREFIX_ . "envialia_tipo_serv ets
																						 on ets.V_COD_TIPO_SERV = eti.V_COD_TIPO_SERV 
																					 where eti.V_COD_TIPO_SERV = v_tipo_serv_cur
																					 order by eti.F_IMPORTE asc;
																DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows_2 = TRUE;
														SET no_more_rows_2 = FALSE;
														OPEN curRango;
															read_loop_2: LOOP 
																	FETCH curRango INTO d_impor_cur, id_carrier_cur;      
																	IF no_more_rows_2 THEN
																	CLOSE curRango;
																	LEAVE read_loop_2;
																END IF;              
																	
																	IF d_impor_cur > d_impor_aux THEN
															insert into " . _DB_PREFIX_ . "range_price(id_carrier, delimiter1, delimiter2) values (id_carrier_cur, d_impor_aux, d_impor_cur);
																		
															END IF;  

															SET d_impor_aux = d_impor_cur;
																	
															END LOOP read_loop_2;
																set d_impor_aux= 0;
															END BLOCK2;
														END LOOP read_loop; 
													END BLOCK1;
													
													BLOCK3: begin
													DECLARE id_carrier_cur INT(10);
														DECLARE id_zona_cur INT(10);
														DECLARE d_impor_cur DECIMAL(20,6);
														DECLARE d_precio_cur DECIMAL(20,6);
														DECLARE no_more_rows_3 BOOLEAN; 
														DECLARE curTar CURSOR FOR select ets.id_carrier, eti.I_COD_ZONA, eti.F_IMPORTE, eti.F_PRECIO 
																											from " . _DB_PREFIX_ . "envialia_tarifa_importes eti 
																				inner join " . _DB_PREFIX_ . "envialia_tipo_serv ets
																					on ets.V_COD_TIPO_SERV = eti.V_COD_TIPO_SERV 
																				order by eti.V_COD_TIPO_SERV, eti.I_COD_ZONA, eti.F_IMPORTE;
																											
														DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows_3 = TRUE;
														SET no_more_rows_3 = FALSE;
														OPEN curTar;
														
														read_loop_3: LOOP
															FETCH curTar INTO id_carrier_cur, id_zona_cur, d_impor_cur, d_precio_cur;    
															IF no_more_rows_3 THEN
															CLOSE curTar;
															LEAVE read_loop_3;
														END IF; 
															
														set d_precio_aux = d_precio_cur;
														set d_precio_aux = d_precio_aux + @d_man_pedido;
															
														IF @d_gasto_envio > 0 then 
														set d_precio_aux = (d_precio_aux) + (d_precio_aux * @d_gasto_envio / 100);
														END IF;
															
														insert into " . _DB_PREFIX_ . "delivery(id_carrier, id_range_price, id_zone, price) 
														select id_carrier_cur, rp.id_range_price, id_zona_cur, d_precio_aux 
														from " . _DB_PREFIX_ . "range_price rp
														where rp.delimiter2 <= d_impor_cur
														and rp.id_carrier = id_carrier_cur
														and rp.id_range_price not in (select d2.id_range_price from " . _DB_PREFIX_ . "delivery d2 
																													where d2.id_carrier = id_carrier_cur
																													  and d2.id_zone = id_zona_cur);
																
														set d_precio_aux = 0;
														END LOOP read_loop_3;    
													END BLOCK3;
													
													if (select ifnull(value, 0) from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_pen') = 1 then
														select @id_serv_grat_pen := ID_CARRIER from " . _DB_PREFIX_ . "envialia_tipo_serv 
														where V_COD_TIPO_SERV = (select value from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_pen_tipo_serv');

														select @d_imp_min_grat_pen := ifnull(value, 0) from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_pen_imp_min';

														update " . _DB_PREFIX_ . "range_price set delimiter2 = @d_imp_min_grat_pen
																where id_carrier = @id_serv_grat_pen
																	and delimiter1 < @d_imp_min_grat_pen
																	and delimiter2 > @d_imp_min_grat_pen;
																
														delete from " . _DB_PREFIX_ . "range_price
														where id_carrier = @id_serv_grat_pen
																	and delimiter2 > @d_imp_min_grat_pen;
																
																delete d from " . _DB_PREFIX_ . "delivery d
														where d.id_range_price not in (select rp.id_range_price from " . _DB_PREFIX_ . "range_price rp)
																	and id_carrier = @id_serv_grat_pen;
														
																insert into " . _DB_PREFIX_ . "range_price(id_carrier, delimiter1, delimiter2) values (@id_serv_grat_pen, @d_imp_min_grat_pen, 9999);
															 
																BLOCK4: begin        
															DECLARE id_carrier_cur INT(10);
															DECLARE id_zona_cur INT(10);
															DECLARE no_more_rows_4 BOOLEAN; 
																	
															DECLARE curTarGra CURSOR FOR select d.id_carrier, d.id_zone
																						 from " . _DB_PREFIX_ . "delivery d 
																															 inner join " . _DB_PREFIX_ . "range_price rp
																																 on rp.id_range_price = d.id_range_price
																															 where rp.delimiter2 = @d_imp_min_grat_pen
																																 and d.id_carrier = @id_serv_grat_pen;
															DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows_4 = TRUE;
															SET no_more_rows_4 = FALSE;
															OPEN curTarGra;
														
															read_loop_4: LOOP
															FETCH curTarGra INTO id_carrier_cur, id_zona_cur;    
															IF no_more_rows_4 THEN
																CLOSE curTarGra;
																LEAVE read_loop_4;
															END IF; 
																		
																		select @id_ran_price_grat := ifnull(id_range_price, 0) 
																		from " . _DB_PREFIX_ . "range_price 
																		where id_carrier = @id_serv_grat_pen 
																		and delimiter1 = @d_imp_min_grat_pen
																		and delimiter2 = 9999;
																		
																		insert into " . _DB_PREFIX_ . "delivery (id_carrier, id_range_price, id_zone, price)
																		values(id_carrier_cur, @id_ran_price_grat, id_zona_cur, 0);
																		
															END LOOP read_loop_4;
															END BLOCK4;
													end if;
														
														if (select ifnull(value, 0) from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_int') = 1 then
														select @id_serv_grat_int := ID_CARRIER from " . _DB_PREFIX_ . "envialia_tipo_serv 
														where V_COD_TIPO_SERV = (select value from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_int_tipo_serv');

														select @d_imp_min_grat_int := ifnull(value, 0) from " . _DB_PREFIX_ . "configuration where name = 'envialia_env_grat_int_imp_min';
																
																update " . _DB_PREFIX_ . "range_price set delimiter2 = @d_imp_min_grat_int
																where id_carrier = @id_serv_grat_int
																	and delimiter1 < @d_imp_min_grat_int
																	and delimiter2 > @d_imp_min_grat_int;
																
														delete from " . _DB_PREFIX_ . "range_price
														where id_carrier = @id_serv_grat_int
																	and delimiter2 > @d_imp_min_grat_int;
																
																delete d from " . _DB_PREFIX_ . "delivery d
														where d.id_range_price not in (select rp.id_range_price from " . _DB_PREFIX_ . "range_price rp)
																	and id_carrier = @id_serv_grat_int;
														
																insert into " . _DB_PREFIX_ . "range_price(id_carrier, delimiter1, delimiter2) values (@id_serv_grat_int, @d_imp_min_grat_int, 9999);
																
																BLOCK5: begin        
															DECLARE id_carrier_cur INT(10);
															DECLARE id_zona_cur INT(10);
															DECLARE no_more_rows_5 BOOLEAN; 
																	
															DECLARE curTarGraInt CURSOR FOR select d.id_carrier, d.id_zone
																							 from " . _DB_PREFIX_ . "delivery d 
																							 inner join " . _DB_PREFIX_ . "range_price rp
																							 on rp.id_range_price = d.id_range_price
																							 where rp.delimiter2 = @d_imp_min_grat_int
																							 and d.id_carrier = @id_serv_grat_int;
															DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows_5 = TRUE;
															SET no_more_rows_5 = FALSE;
															OPEN curTarGraInt;
														
															read_loop_5: LOOP
															FETCH curTarGraInt INTO id_carrier_cur, id_zona_cur;    
															IF no_more_rows_5 THEN
																CLOSE curTarGraInt;
																LEAVE read_loop_5;
															END IF; 
																		
																		select @id_ran_price_grat := ifnull(id_range_price, 0) 
																		from " . _DB_PREFIX_ . "range_price 
																		where id_carrier = @id_serv_grat_int 
																		and delimiter1 = @d_imp_min_grat_int
																		and delimiter2 = 9999;
																		
																		insert into " . _DB_PREFIX_ . "delivery (id_carrier, id_range_price, id_zone, price)
																		values(id_carrier_cur, @id_ran_price_grat, id_zona_cur, 0);
																		
															END LOOP read_loop_5;
															END BLOCK5;
														end if;
													
													update " . _DB_PREFIX_ . "configuration set value = '1' where name = 'envialia_conf_tar';
													update " . _DB_PREFIX_ . "carrier set shipping_method = 2 where external_module_name = 'envialiacarrier';
												END;");