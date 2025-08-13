const createClientsPanel = () => {
    Ext.define("App.model.Cliente", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "email", type: "string" },
            { name: "telefono", type: "string" },
            { name: "direccion", type: "string" },

            // Campos de PersonaNatural
            { name: "nombres", type: "string", defaultValue: null },
            { name: "apellidos", type: "string", defaultValue: null },
            { name: "cedula", type: "string", defaultValue: null },

            // Campos de PersonaJuridica
            { name: "razonSocial", type: "string", defaultValue: null },
            { name: "ruc", type: "string", defaultValue: null },
            { name: "representanteLegal", type: "string", defaultValue: null }
        ]
    });

    let clienteStore = Ext.create("Ext.data.Store", {
        storeId: "clienteStore",
        model: "App.model.Cliente",
        proxy: {
            type: "rest",
            url: "/Api/cliente.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: "json", rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Clientes",
        store: clienteStore,
        itemId: "clientePanel",
        layout: "fit",
        columns: [
            { text: "ID", dataIndex: "id", width: 50 },
            { text: "Email", dataIndex: "email", flex: 1 },
            { text: "Teléfono", dataIndex: "telefono", flex: 1 },
            { text: "Dirección", dataIndex: "direccion", flex: 1 },
            { text: "Nombres", dataIndex: "nombre", flex: 1 },
            { text: "Apellidos", dataIndex: "apellido", flex: 1 },
            { text: "Cédula", dataIndex: "cedula", flex: 1 },
            { text: "Razón Social", dataIndex: "razonSocial", flex: 1 },
            { text: "RUC", dataIndex: "ruc", flex: 1 },
            { text: "Representante Legal", dataIndex: "representanteLegal", flex: 1 }
        ],
    });

    return grid;
};
