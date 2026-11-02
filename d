[33m70228fc[m[33m ([m[1;31morigin/main[m[33m, [m[1;31morigin/HEAD[m[33m, [m[1;32mmain[m[33m)[m FUNCIONALIDAD DE ENVIO DE CORREO  GMAIL DEN OTIFICAIONES EN ALERTA FASE 1.0 BETA
 app/Commands/EnviarVencimientos.php          |  26 [32m++[m
 app/Config/Routes.php                        |   5 [32m+[m[31m-[m
 app/Controllers/DashboardController.php      |   2 [32m+[m
 app/Controllers/NotificacionesController.php | 193 [32m+++++++++++[m[31m-[m
 app/Controllers/PersonasAddController.php    |  58 [32m+++[m[31m-[m
 app/Libraries/NotificadorVencimientos.php    | 334 [32m++++++++++++++++++++[m
 app/Models/PagosModel.php                    |   2 [32m+[m[31m-[m
 app/Views/configuracion.php                  | 227 [32m+++++++++++[m[31m---[m
 app/Views/layouts/topbar.php                 | 182 [32m++++++[m[31m-----[m
 app/Views/notificaciones/notificaciones.php  | 450 [32m+++++++++++++++++++++++[m[31m----[m
 app/Views/personas/partials/_modal_pago.php  |   5 [32m+[m
 enviar_vencimientos.bat                      |   3 [32m+[m
 public/js/persona-editor.js                  |  49 [32m+[m[31m--[m
 type                                         |   0
 xd.bat.txt                                   |   3 [32m+[m
 15 files changed, 1307 insertions(+), 232 deletions(-)
