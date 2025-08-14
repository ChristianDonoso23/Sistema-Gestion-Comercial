const createUsersPanel = () => {
    Ext.define("App.model.Usuario", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "username", type: "string" },
            { name: "passwordHash", type: "string" },
            { name: "estado", type: "string" },
        ]
    });

    let userStore = Ext.create("Ext.data.Store", {
        storeId: "userStore",
        model: "App.model.Usuario",
        proxy: {
            type: "rest",
            url: "/Api/usuario.php",
            reader: { type: "json", rootProperty: "" },
            writer: { type: "json", rootProperty: "", writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Usuarios",
        store: userStore,
        itemId: "usersPanel",
        layout: "fit",
        columns: [
            { 
                text: "ID", 
                width: 50, 
                sortable: false, 
                hideable: false, 
                dataIndex: "id" 
            },
            { 
                text: "Username", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "username" 
            },
            { 
                text: "Password Hash", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "passwordHash" 
            },
            { 
                text: "Estado", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "estado" 
            }
        ],
        tbar: [
            {
                text: 'Eliminar Usuario',
                handler() {
                    const rec = this.up('grid').getSelection()[0];
                    if (!rec) return Ext.Msg.alert('Atención', 'Seleccione un Usuario para eliminar.');

                    Ext.Msg.confirm('Confirmar', '¿Está seguro de que desea eliminar el siguiente Usuario?', btn => {
                        if (btn === 'yes') {
                            userStore.remove(rec);
                            userStore.sync({
                                success: () => Ext.Msg.alert('Éxito', 'Usuario eliminada correctamente.'),
                                failure: () => Ext.Msg.alert('Error', 'No se pudo eliminar el Usuario.')
                            });
                        }
                    });
                }
            }
        ]
    });

    return grid;
};
