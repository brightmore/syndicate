<div class="row">
    <div class="col-lg-4">

        <div class="content-wrapper" id="sharedAmount">
            <p>Amount to be shared if two Numbers drop </p>
            <span class="winning_amount"> Ghc <?php echo $currentNumber->amount ?></span>

            <hr />

            <form>
                <p>Calculate Amount you can win base on your bet.</p>
                <div class="input-group">
                    <span class="input-group-addon">GHc</span>
                    <input type="text" class="form-control" aria-label="Amount (to the nearest Ghana cedis)">
                    <span class="input-group-addon">.00</span>
                </div>
                <p id="amount_to_win"></p>
            </form>
        </div>

        <div id="place_bet" class="content-wrapper">
            <div class="header"> Place a Bet</div>
            <div class="content">
              <form>
                <input type="text" id="placebet" name="placebet" class="form-control" />
                <?php echo form_input($csrf) ?>
                <div class="text-right">
                <button name="submit_bet" type="submit" id="submit_betting" class="btn btn-default">Bet Now</button>
                </div>
              </form>
            </div>
        </div>

        <div class="content-wrapper">
            <div style="background-color: #FFF;height: 150px"></div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row">
            <div class="col-lg-6">
                <div class="content-wrapper">

                    <div id="today_numbers">
                        <div class="header">Current Machine Numbers</div>
                        <div class="content">
                             <div class="table-responsive">
                                    <div class="today_numbers">
                                        <?php echo $currentNumber->numbers ?>
                                    </div>

                             </div>
                        </div>
                    </div>
                </div>
                <div class="content-wrapper">
                    <div class="header">Total Bet Place</div>
                    <div class="content">

                        <div class="total_bet" style="padding: 10px 0; border-bottom:solid 1px #fff">Total Amount of Bet place today: GHc <?php echo $total_bet_placed ?></div>
                        <div class="my_bet">
                          <span>Bet Placed today: </span><span>Ghc <?php echo $bet_place_today ?></span>
                             <!-- <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <td>Bet Placed</td>
                                    <td>Increase Bet</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <select class="select-bet">
                                            <option>Select Amount</option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>5</option>
                                            <option>10</option>
                                            <option>15</option>
                                            <option>20</option>
                                            <option>30</option>
                                            <option>50</option>
                                        </select>

                                    </td>
                                </tr>

                            </table>
                             </div> -->
                            <div style="margin:20px 0;">
                                <a href="#" style="background: yellow; color:#262626" class="btn" data-toggle="modal" data-target=".redraw_my_bet" title="you want to redraw the bet you placed today">REDRAW MY BETTING</a>
                            </div>

                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">Go!</button>
                            </span>
                        </div><!-- /input-group -->
                        <div class="table-responsive">
                            <?php if($people_bets_today) {?>
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Bet Place</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($people_bets_today as $value) { ?>
                                        <tr>
                                            <td><?php echo $value->username ?></td>
                                            <td>Ghc <?php echo $value->amount ?></td>
                                            <td><?php echo $value->time ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php }else{ ?>

                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content-wrapper">
                    <div class="header"> Last Week and This Week Numbers</div>
                    <div class="content">
                        <div class="table-responsive">
                            <?php if($numbers_draw) {?>
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Number</th>
                                        <th>status</th>
                                        <th>Ghc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($numbers_draw as $value) { ?>
                                        <tr>
                                            <td><?php echo $value->date ?></td>
                                            <td><?php echo $value->numbers ?></td>
                                            <td> <?php if($value->status === 0){ ?><span class="fa fa-2x fa-arrow-circle-o-up up"></span><?php }else{ ?><span class="fa fa-2x fa-arrow-circle-o-down down " title="lose"></span> <?php }?> </td>
                                            <td><?php echo $value->amount ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php }else{ ?>
                            <!-- @todo -->
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div id="transaction_logs">
                    <div class="content-wrapper">
                        <div class="header">Transaction Log</div>

                        <table>
                            <tr>
                                <td>
                                   <div>
                                       <select class="select">
                                        <option>Select Amount</option>
                                        <option value="placing_betting">Betting</option>
                                        <option value="credit_acccount">Top up</option>
                                        <option value="company_paying_you">Payment</option>
                                        <option value="sms">SMS</option>
                                        <option value="debit_account">Debits</option>
                                        <option value="supended_account"></option>
                                    </select>
                            </div>
                                </td>
                                <td>
                                     <input type="text" class="form-control" aria-label="...">
                                </td>
                            </tr>
                        </table>

                        <div class="transactions">
                            <div class="table-responsive">
                                <?php if($transaction_activities){ ?>

                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Date</th>
                                            <th>GHc</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                          <?php foreach ($transaction_activities as $value) { ?>
                                                <tr>
                                                    <td><?php echo $value->description ?></td>
                                                    <td><?php echo $value->date_created ?></td>
                                                    <td><?php echo $value->amount ?></td>
                                                    <td>
                                                        <?php if($value->state == 1){ ?>
                                                            <span class="fa fa-arrow-right up"></span></td>
                                                        <?php }else {?>
                                                            <span class="fa fa-arrow-right down"></span></td>
                                                        <?php } ?>

                                                </tr>

                                           <?php } ?>
                                    </tbody>
                                </table>
                                <?php }else{ ?>
                                    <!--- @todo -->
                                <?php } ?>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!--model-->
<div class="modal slide redraw_my_bet" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div>
                You sure want to redraw your betting. Think about it again, just see the amount of money you can win today
            </div>
            <div>
                <button class="btn btn-danger">YES</button> <a href="#" class="btn btn-success">NO</a>
            </div>
        </div>
    </div>
</div>

<!--contact us-->

<div class="modal slide contact_us" tabindex="-1" role="dialog" aria-labelledby="contactus">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            contact us
        </div>
    </div>
</div>
