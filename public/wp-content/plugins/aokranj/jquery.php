<div id="tabs">
    <ul>
        <li><a href="#dodaj">Dodaj vzpon</a></li>
        <li><a href="#seznam">Seznam vzponov</a></li>
    </ul>
    
    <div id="dodaj">
        <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" name="dodajvzpon" id="dodajvzpon" class="validate">
            
            <input name="action" type="hidden" value="dodajvzpon" />
            
            <?php wp_nonce_field('dodaj-vzpon', '_wpnonce_dodaj-vzpon'); ?>
            
            <table class="form-table">
                
                <!-- Tip -->
                <tr class="form-field">
                    <th scope="row"><label for="tip">Vrsta</label></th>
                    <td><select name="tip" id="tip">
                            <option value=" " selected disabled hidden>-- Izberi tip vzpona --</option>
                            <option value="ALP">alpinistična smer</option>
                            <option value="ŠP">športno plezalna smer</option>
                            <option value="SMUK">smuk</option>
                            <option value="PR">pristop</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Destinacija -->
                <tr class="form-field form-required">
                    <th scope="row"><label for="destinacija">Destinacija <span class="description">(obvezno)</span></label></th>
                    <td><input name="destinacija" type="text" id="destinacija" value="" aria-required="true" /></td>
                </tr>
                
                <!-- Smer -->
                <tr class="form-field form-required">
                    <th scope="row"><label for="smer">Smer <span class="description">(obvezno)</span></label></th>
                    <td><input name="smer" type="text" id="smer" value="" aria-required="true" /></td>
                </tr>
                
                <!-- Datum -->
                <tr class="form-field form-required">
                    <th scope="row"><label for="Datum">Datum <span class="description">(obvezno)</span></label></th>
                    <td><input name="datum" type="text" id="datum" value="" aria-required="true" class="small" /></td>
                </tr>
                
                <!-- Ocena -->
                <tr class="form-field">
                    <th scope="row"><label for="ocena">Ocena</label></th>
                    <td><input name="ocena" type="text" id="ocena" value="" aria-required="true" class="small" /></td>
                </tr>
                
                <!-- Čas -->
                <tr class="form-field">
                    <th scope="row"><label for="cas">Čas</label></th>
                    <td><input name="cas" type="text" id="cas" value="" aria-required="true" class="small" /></td>
                </tr>
                
                <!-- Vrsta -->
                <tr class="form-field form-required">
                    <th scope="row"><label for="vrsta">Vrsta <span class="description">(obvezno)</span></label></th>
                    <td><select name="vrsta" id="vrsta">
                            <option value="" selected disabled hidden>-- Izberi vrsto vzpona --</option>
                            <option value="K">kopna</option>
                            <option value="L">ledna (snežna)</option>
                            <option value="LK">ledna kombinirana</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Višina smeri -->
                <tr class="form-field form-required">
                    <th scope="row"><label for="visina_smer">Višina smeri <span class="description">(obvezno)</span></label></th>
                    <td><input name="visina_smer" type="text" id="visina_smer" value="" aria-required="true" class="small" /> m</td>
                </tr>
                
                <!-- Nadm. viš. izstopa -->
                <tr class="form-field">
                    <th scope="row"><label for="visina_izstop">Nadm. viš. izstopa</label></th>
                    <td><input name="visina_izstop" type="text" id="visina_izstop" value="" aria-required="true" class="small"  /> m</td>
                </tr>
                
                <!-- Vrsta ponovitve -->
                <tr class="form-field">
                    <th scope="row"><label for="pon_vrsta">Vrsta ponovitve</label></th>
                    <td><select name="pon_vrsta" id="pon_vrsta">
                            <option value="" selected>-- Ni ponovitev --</option>
                            <option value="Prv">prvenstvena</option>
                            <option value="1P">prva ponovitev</option>
                            <option value="2P">druga ponovitev</option>
                            <option value="ZP">zimska ponovitev</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Način ponovitve -->
                <tr class="form-field">
                    <th scope="row"><label for="pon_nacin">Način ponovitve</label></th>
                    <td><select name="pon_nacin" id="pon_nacin">
                            <option value="" selected>-- Ni ponovitev --</option>
                            <option value="PP">prosta ponovitev</option>
                            <option value="NP">na pogled</option>
                            <option value="RP">z rdečo piko</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Stil -->
                <tr class="form-field">
                    <th scope="row"><label for="stil">Stil</label></th>
                    <td><select name="stil" id="stil">
                            <option value="" selected>-- Izberite stil vzpona --</option>
                            <option value="A">alpski</option>
                            <option value="K">kombinirani</option>
                            <option value="OS">odpravarski</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Mesto -->
                <tr class="form-field">
                    <th scope="row"><label for="ascent_type">Mesto</label></th>
                    <td><select name="mesto" id="mesto">
                            <option value="" selected>-- Izberite vaše mesto pri vzponu --</option>
                            <option value="V">vodstvo</option>
                            <option value="D">drugi</option>
                            <option value="Ž">žimarjenje</option>
                            <option value="I">izmenjaje</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Soplezalec -->
                <tr class="form-field">
                    <th scope="row"><label for="partner">Soplezalec</label></th>
                    <td><input name="partner" type="text" id="partner" value="" aria-required="true" /></td>
                <!--</tr>-->
                
                <!-- Opomba -->
                <tr class="form-field">
                    <th scope="row"><label for="opomba">Opomba</label></th>
                    <td><select name="opomba" id="opomba">
                            <option value="" selected>-- Brez opombe --</option>
                            <option value="ZS">zaledeneli slap</option>
                        </select>
                    </td>
                </tr>
                
            </table>
        
            <?php submit_button( __( 'Dodaj vzpon '), 'primary', 'dodajvzpon', true/*, array('disabled' => 'disabled')*/); ?>
            
        </form>
    </div>
    
    <div id="seznam">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
</div>
