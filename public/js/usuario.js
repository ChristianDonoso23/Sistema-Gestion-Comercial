const createUsersPanel = () => {
    Ext.define("App.model.Usuario", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "username", type: "string" },
            { name: "passwordHash", type: "string" },
            { name: "estado", type: "string" },
            { name: "rolNombre", type: "string" } // opcional: mostrar el rol del usuario
        ]
    });

    let userStore = Ext.create("Ext.data.Store", {
        storeId: "userStore",
        model: "App.model.Usuario",
        proxy: {
            type: "rest",
            url: "/api/usuario.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: 'json', rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Usuarios",
        store: userStore,
        itemId: "usersPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 50, dataIndex: "id" },
            { text: "Username", flex: 1, dataIndex: "username" },
            { text: "Password Hash", flex: 1, dataIndex: "passwordHash" },
            { text: "Estado", flex: 1, dataIndex: "estado" }
        ]
    });

    return grid;
};
