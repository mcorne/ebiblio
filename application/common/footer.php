        <script>

            function close_dropdown(id)
            {
                var element = document.getElementById(id);

                element.className    = element.className.replace(" w3-show", "");
                element.style.zIndex = "-1";
            }

            function close_sidebar()
            {
                document.getElementById("overlay").style.display = "none";
                document.getElementById("sidebar").style.display = "none";

                close_dropdown('manage_books');
                close_dropdown('my_account');
            }

            function open_dropdown(id)
            {
                var element = document.getElementById(id);

                element.className    += " w3-show";
                element.style.zIndex = "1";
            }

            function open_or_close_dropdown(id)
            {
                var element = document.getElementById(id);

                if (element.className.indexOf("w3-show") == -1) {
                    open_dropdown(id);

                    if (id != 'manage_books') {
                        close_dropdown('manage_books');
                    }

                    if (id != 'my_account') {
                        close_dropdown('my_account');
                    }
                } else {
                    close_dropdown(id);
                }
            }

            function open_sidebar()
            {
                document.getElementById("overlay").style.display = "block";
                document.getElementById("sidebar").style.display = "block";
            }

        </script>

    </body>
</html>
