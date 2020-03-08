<template>
	<div id="user-management-page-component">
		<div class="row m-0">
			<div class="col-12 px-0" :key="'update-record-count-'+update_record_count">
				<el-button size="small" type="primary" v-on:click="user_form_action = 'create'; show_user_form = true;">
					Create New User
				</el-button>
				<hr>

				<el-table
					:data="users_list"
					style="width: 100%"
				>
					<el-table-column
						label="First Name"
						prop="first_name"
					>
					</el-table-column>

					<el-table-column
						label="Last Name"
						prop="last_name"
					>
					</el-table-column>

					<el-table-column
						label="Gender"
						prop="gender"
					>
					</el-table-column>

					<el-table-column
						label="Status"
						prop="status"
					>
						<template slot-scope="scope">
							<span v-if="!scope.row.status">
								Enabled
							</span>
							<span v-else>
								Disabled
							</span>
						</template>
					</el-table-column>

					<el-table-column
						label="Account Type"
						prop="account_type"
					>
						<template slot-scope="scope">
							{{ scope.row.account_type | capitalize }}
						</template>
					</el-table-column>

					<el-table-column
						label=""
						prop="action_column"
					>
						<template slot-scope="scope">
							<div style="width: 100%; text-align: right;">
								<span v-if="!scope.row.status">
									<el-button size="small" type="danger" v-on:click="changeUserStatus(scope.row, 'disable')">
										Disable
									</el-button>
								</span>
								<span v-else>
									<el-button size="small" type="success" v-on:click="changeUserStatus(scope.row, 'enable')">
										Enable
									</el-button>
								</span>
							</div>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</div>

		<el-dialog 
			title="User Form" 
			:visible.sync="show_user_form" 
			width="40%" 
			:show-close="false" 
			:close-on-click-modal="false" 
			:close-on-press-escape="false"
		>
			<user-form 
				:user="selected_user" 
				v-if="show_user_form" 
				:submitting="submitting_user_form"
				:action="user_form_action"
				v-on:aftersubmit="handleUserFormAfterSubmit($event)"
			></user-form>
			
			<span slot="footer" class="dialog-footer">
				<el-button size="small" type="success" v-on:click="submitting_user_form = true">
					Submit
				</el-button>
				<el-button size="small" type="danger" v-on:click="show_user_form = false">
					Close
				</el-button>
			</span>
		</el-dialog>
	</div>
</template>
<script type="text/javascript">
	export default {
		components: {
			'user-form': require('./components/user-form.vue').default
		},
		data(){
			return {
				users_list: [],
				show_user_form: false,
				submitting_user_form: false,
				selected_user: null,
				user_form_action: 'create',

				update_record_count: 0,
			}
		},
		created(){
			this.$store.dispatch('pageLoader', { display: true, message: 'Fetching Users List. Please Wait...' });
			this.$axios.get('/api/get-all-users', {}).then((response) => {

				for(let counter = 0; counter < response.data.users.length; counter++){
					this.users_list.push(this.setUserObject(response.data.users[counter]));
				}

				this.$store.dispatch('pageLoader', { display: false, message: '' });
			}).catch((error) => {
				this.$store.dispatch('pageLoader', { display: false, message: '' });
			});
		},

		methods: {
			setUserObject(user_object){
				return {
					full_name: user_object.profile.first_name + ' ' + user_object.profile.last_name,
					first_name: user_object.profile.first_name,
					last_name: user_object.profile.last_name,
					gender: user_object.profile.gender,
					account_type: user_object.user_role.role.display_name,
					status: user_object.is_disabled,
					raw_info: user_object,
					action_column: null
				};
			},

			handleUserFormAfterSubmit(param){
				if(param.data){
					let success_message = 'User successfully updated.';

					if(param.action == 'create'){
						this.users_list.push(this.setUserObject(param.data));
						this.show_user_form = false;
						this.success_message = 'User successfully created.';
					}

					this.$message({
			          	message: this.success_message,
			          	showClose: true,
			          	type: 'success'
			        });

			        this.update_record_count++;
				}

				this.submitting_user_form = param.submitting;
				console.log("HANDLE USER FORM AFTER SUBMIT: ", param);
			},

			changeUserStatus(user_info, action){
				console.log("USER INFO:", user_info);
				console.log("ACTION: ", action);

				let message = 'Enabling account. Please wait...';
				if(action == 'disable'){
					message = 'Disabling account. Please wait...';
				}

				this.$store.dispatch('pageLoader', { display: true, message: message });
				this.$axios.post('/api/change-user-status/'+action, { id: user_info.raw_info.id }).then((response) => {
					this.$store.dispatch('pageLoader', { display: false, message: '' });

					let new_status = 0;
					if(action == 'disable'){
						new_status = 1;
					}

					for(let counter = 0; counter < this.users_list.length; counter++){
						if(this.users_list[counter]['raw_info']['id'] == user_info.raw_info.id){
							this.users_list[counter]['status'] = new_status;
							this.users_list[counter]['raw_info']['is_disabled'] = new_status;
							break;
						}
					}

					this.update_record_count++;

					this.$message({
			          	message: 'User status successfully changed.',
			          	showClose: true,
			          	type: 'success'
			        });

				}).catch((error) => {
					console.log(error);

					this.$store.dispatch('pageLoader', { display: false, message: '' });
				});
			}
		}
	}
</script>