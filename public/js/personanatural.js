const createNaturalPersonPanel = () => {
    Ext.define("App.model.NaturalPerson", {
        extend: "Ext.data.Model",
        fields: [
            { name: "cliente_id", type: "int" },
            { name: "email", type: "string" },
            { name: "telefono", type: "string" },
            { name: "direccion", type: "string" },
            { name: "nombre", type: "string" },
            { name: "apellido", type: "string" },
            { name: "cedula", type: "string" }
        ]
    });

    let naturalPersonStore = Ext.create("Ext.data.Store", {
        storeId: "naturalPersonStore",
        model: "App.model.NaturalPerson",
        proxy: {
            type: "rest",
            url: "/api/personanatural.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: 'json', rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Personas Naturales",
        store: naturalPersonStore,
        itemId: "naturalPersonPanel",
        layout: "fit",
        columns: [
            {
                text: "ID Cliente",
                width: 80,
                dataIndex: "id",
                sortable: false,
                hideable: false,
            },
            {
                text: "Email",
                flex: 1,
                dataIndex: "email",
                sortable: false,
                hideable: false,
            },
            {
                text: "Teléfono",
                flex: 1,
                dataIndex: "telefono",
                sortable: false,
                hideable: false,
            },
            {
                text: "Dirección",
                flex: 1,
                dataIndex: "direccion",
                sortable: false,
                hideable: false,
            },
            {
                text: "Nombre",
                flex: 1,
                dataIndex: "nombre",
                sortable: false,
                hideable: false,
            },
            {
                text: "Apellido",
                flex: 1,
                dataIndex: "apellido",
                sortable: false,
                hideable: false,
            },
            {
                text: "Cédula",
                flex: 1,
                dataIndex: "cedula",
                sortable: false,
                hideable: false,
            },
        ],
    });

    return grid;
};
